<?php

namespace App\Services;

use Aws\Textract\TextractClient;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AwsTextractService
{
    protected TextractClient $textractClient;
    protected S3Client       $texS3Client;

    /** Textract-dedicated S3 bucket (TEX_S3_BUCKET_NAME) */
    protected string $texBucket;

    public function __construct()
    {
        // Dedicated AWS credentials for Textract + Textract S3 bucket
        $texCredentials = [
            'key'    => env('TEX_AWS_ACCESS_KEY_ID'),
            'secret' => env('TEX_AWS_SECRET_ACCESS_KEY'),
        ];
        $texRegion = env('TEX_AWS_REGION', 'us-east-1');

        $this->textractClient = new TextractClient([
            'version'     => 'latest',
            'region'      => $texRegion,
            'credentials' => $texCredentials,
        ]);

        // S3 client for the Textract bucket (same credentials as Textract)
        $this->texS3Client = new S3Client([
            'version'     => 'latest',
            'region'      => $texRegion,
            'credentials' => $texCredentials,
        ]);

        $this->texBucket = env('TEX_S3_BUCKET_NAME');
    }

    /**
     * Full pipeline:
     * 1. Download the document from the app's S3 (Storage::disk('s3'))
     * 2. Upload it to the Textract S3 bucket (TEX_S3_BUCKET_NAME)
     * 3. Run Textract on it
     * 4. Delete the temp file from Textract bucket
     *
     * @param  string $appS3Key  Key in the app's main S3 bucket (e.g. "patientdocument/1/abc.pdf")
     * @return string  Extracted plain text
     */
    public function extractTextFromS3(string $appS3Key): string
    {
        $extension = strtolower(pathinfo($appS3Key, PATHINFO_EXTENSION));

        // Files are stored in the TEX S3 bucket (texS3Client / texBucket).
        // Check if the file exists there directly; if so run Textract on it without copying.
        // If not found (e.g. different bucket setup), fall back to downloading via texS3Client
        // and uploading to a temp key.
        try {
            $this->texS3Client->headObject([
                'Bucket' => $this->texBucket,
                'Key'    => $appS3Key,
            ]);
            // File exists in TEX bucket — run Textract directly, no copy needed
            if ($extension === 'pdf') {
                return $this->extractAsyncPdf($appS3Key);
            }
            return $this->extractSyncImage($appS3Key);
        } catch (\Aws\Exception\AwsException $e) {
            // File not at that key — fall through to download+copy approach
            Log::warning('[Textract] File not found in Textract bucket: ' . $appS3Key);
        }

        // Fallback: download from TEX bucket using the resolved key, upload to temp key
        try {
            $result       = $this->texS3Client->getObject([
                'Bucket' => $this->texBucket,
                'Key'    => $appS3Key,
            ]);
            $fileContents = (string) $result['Body'];
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException('Could not download file from app S3: ' . $appS3Key);
        }

        $tempKey = 'mdo-textract-temp/' . uniqid('doc_', true) . '.' . $extension;

        $this->texS3Client->putObject([
            'Bucket' => $this->texBucket,
            'Key'    => $tempKey,
            'Body'   => $fileContents,
        ]);

        try {
            if ($extension === 'pdf') {
                $text = $this->extractAsyncPdf($tempKey);
            } else {
                $text = $this->extractSyncImage($tempKey);
            }
        } finally {
            try {
                $this->texS3Client->deleteObject([
                    'Bucket' => $this->texBucket,
                    'Key'    => $tempKey,
                ]);
            } catch (\Throwable $e) {
                Log::warning('[Textract] Failed to delete temp file: ' . $tempKey . ' | ' . $e->getMessage());
            }
        }

        return $text;
    }

    /**
     * Extract text from a local temp file (e.g. from an uploaded request file).
     * Uploads to Textract S3 bucket, runs extraction, then cleans up.
     *
     * @param  string $localPath   Absolute path to the temp file
     * @param  string $extension   File extension: pdf, jpg, png, etc.
     * @return string  Extracted plain text
     */
    public function extractTextFromLocalFile(string $localPath, string $extension): string
    {
        $extension = strtolower($extension);
        $tempKey   = 'mdo-textract-temp/' . uniqid('doc_', true) . '.' . $extension;

        $this->texS3Client->putObject([
            'Bucket'     => $this->texBucket,
            'Key'        => $tempKey,
            'Body'       => file_get_contents($localPath),
            'ContentType'=> $extension === 'pdf' ? 'application/pdf' : 'image/' . $extension,
        ]);

        try {
            $text = $extension === 'pdf'
                ? $this->extractAsyncPdf($tempKey)
                : $this->extractSyncImage($tempKey);
        } finally {
            try {
                $this->texS3Client->deleteObject(['Bucket' => $this->texBucket, 'Key' => $tempKey]);
            } catch (\Throwable $e) {
                Log::warning('[Textract] Failed to delete temp file: ' . $tempKey . ' | ' . $e->getMessage());
            }
        }

        return $text;
    }

    /**
     * Synchronous text detection for single-page images (JPG, PNG, TIFF).
     */
    protected function extractSyncImage(string $s3Key): string
    {
        $result = $this->textractClient->detectDocumentText([
            'Document' => [
                'S3Object' => [
                    'Bucket' => $this->texBucket,
                    'Name'   => $s3Key,
                ],
            ],
        ]);

        return $this->blocksToText($result['Blocks'] ?? []);
    }

    /**
     * Async text detection for multi-page PDFs.
     * Polls until job completes (max 60 attempts × 5 s = 5 min).
     */
    protected function extractAsyncPdf(string $s3Key): string
    {
        $startResult = $this->textractClient->startDocumentTextDetection([
            'DocumentLocation' => [
                'S3Object' => [
                    'Bucket' => $this->texBucket,
                    'Name'   => $s3Key,
                ],
            ],
        ]);

        $jobId       = $startResult['JobId'];
        $maxAttempts = 60;
        $attempt     = 0;
        $status      = '';
        $blocks      = [];

        do {
            sleep(5);
            $result  = $this->textractClient->getDocumentTextDetection(['JobId' => $jobId]);
            $status  = $result['JobStatus'];
            $attempt++;

            if ($status === 'SUCCEEDED') {
                $blocks = array_merge($blocks, $result['Blocks'] ?? []);

                // Paginate through all result pages
                while (!empty($result['NextToken'])) {
                    $result = $this->textractClient->getDocumentTextDetection([
                        'JobId'     => $jobId,
                        'NextToken' => $result['NextToken'],
                    ]);
                    $blocks = array_merge($blocks, $result['Blocks'] ?? []);
                }

                break;
            }

            if ($status === 'FAILED') {
                throw new \RuntimeException('AWS Textract async job failed: ' . ($result['StatusMessage'] ?? 'Unknown error'));
            }

        } while ($attempt < $maxAttempts);

        if ($status !== 'SUCCEEDED') {
            throw new \RuntimeException('AWS Textract job timed out after ' . ($maxAttempts * 5) . ' seconds.');
        }

        return $this->blocksToText($blocks);
    }

    /**
     * Convert Textract LINE blocks to plain text string.
     */
    protected function blocksToText(array $blocks): string
    {
        $lines = [];
        foreach ($blocks as $block) {
            if ($block['BlockType'] === 'LINE') {
                $lines[] = $block['Text'] ?? '';
            }
        }
        return implode("\n", $lines);
    }

    /**
     * Extract text from raw file contents (bytes already in memory).
     * Uploads to a temp key in the Textract bucket, runs Textract, then deletes.
     */
    public function extractTextFromContents(string $fileContents, string $extension): string
    {
        $tempKey = 'mdo-textract-temp/' . uniqid('doc_', true) . '.' . $extension;

        $this->texS3Client->putObject([
            'Bucket' => $this->texBucket,
            'Key'    => $tempKey,
            'Body'   => $fileContents,
        ]);

        try {
            if ($extension === 'pdf') {
                $text = $this->extractAsyncPdf($tempKey);
            } else {
                $text = $this->extractSyncImage($tempKey);
            }
        } finally {
            try {
                $this->texS3Client->deleteObject([
                    'Bucket' => $this->texBucket,
                    'Key'    => $tempKey,
                ]);
            } catch (\Throwable $e) {
                Log::warning('[Textract] Failed to delete temp file: ' . $tempKey . ' | ' . $e->getMessage());
            }
        }

        return $text;
    }
}
