<?php

namespace App\Services;

use Aws\BedrockRuntime\BedrockRuntimeClient;
use Aws\Exception\AwsException;

class AwsBedrockService
{
    protected BedrockRuntimeClient $client;
    protected string $modelId;

    public function __construct()
    {
        $this->client = new BedrockRuntimeClient([
            'version'     => 'latest',
            'region'      => env('BEDROCK_REGION', env('AWS_REGION')),
            'credentials' => [
                'key'    => env('BEDROCK_ACCESS_KEY_ID', env('AWS_ACCESS_KEY_ID')),
                'secret' => env('BEDROCK_SECRET_ACCESS_KEY', env('AWS_SECRET_ACCESS_KEY')),
            ],
        ]);

        $this->modelId = env('BEDROCK_MODEL_ID', 'us.anthropic.claude-3-haiku-20240307-v1:0');
    }

    /**
     * Send extracted MDO 485 text to Claude via Bedrock and return parsed JSON array.
     *
     * @param  string $extractedText  Plain text from Textract
     * @return array{parsed: array, raw: string}
     */
    public function analyzeMedicalDocument(string $extractedText): array
    {
        $prompt = $this->buildPrompt($extractedText);

        $body = json_encode([
            'anthropic_version' => 'bedrock-2023-05-31',
            'max_tokens'        => 4096,
            'messages'          => [
                [
                    'role'    => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);

        $result = $this->client->invokeModel([
            'modelId'     => $this->modelId,
            'contentType' => 'application/json',
            'accept'      => 'application/json',
            'body'        => $body,
        ]);

        $rawResponse = (string) $result['body'];
        $response    = json_decode($rawResponse, true);

        // Extract text content from Claude's response
        $textContent = $response['content'][0]['text'] ?? '';

        // Parse the JSON block from Claude's reply
        $parsed = $this->parseJsonFromText($textContent);

        return [
            'parsed' => $parsed,
            'raw'    => $rawResponse,
        ];
    }

    /**
     * Build the Claude prompt as specified in the requirement document.
     */
    protected function buildPrompt(string $extractedText): string
    {
        return <<<PROMPT
You are an advanced medical documentation AI assistant.

Your task is to analyze the following medical document text. It could be ANY type of clinical document — lab report, toxicology screen, MDO 485, prescription, imaging report, discharge summary, etc.

Step 1 — Identify the document and set these two fields:
- "document_type": ONE of — "lab_report", "toxicology", "imaging", "prescription", "medical_form", "discharge", "other"
  - lab_report   = blood work, urine tests, cultures, panels
  - toxicology   = drug of abuse screens, tox screens
  - imaging      = X-ray, MRI, CT scan, ultrasound reports
  - prescription = medication orders, Rx documents
  - medical_form = MDO 485, OASIS, care plans, clinical notes
  - discharge    = discharge summaries
  - other        = anything else
- "document_label": a SHORT human-readable name describing exactly what this specific document is (e.g. "Drug of Abuse Toxicology Screen", "Complete Blood Count Panel", "MDO 485 Home Health Certification", "Chest X-Ray Report", "Discharge Summary"). Do NOT use generic words like "lab_report" or "soap". Max 6 words.

Step 2 — For ALL document types produce these fields:
- "patient_info": { name, dob, mobile } — extract exactly as written; null if not found.
- "short_summary": 2-3 sentences that capture WHAT the document is and its KEY finding. Be specific:
  - Lab/tox: name the test panel and state whether results are normal/abnormal/negative/positive.
  - Imaging: state the body part, modality, and primary impression.
  - Medical form: state the primary diagnosis/condition and care plan highlights.
  - Prescription: state the medications and purpose.
  - Other: summarise the main clinical content.
- "overall_result": single word — "Negative", "Positive", "Normal", "Abnormal", "Mixed", or "N/A".
- "red_flags": array of strings — out-of-range values, critical findings, urgent items. Empty array [] if none.

Step 3 — For "medical_form" and "discharge" documents additionally produce:
- "soap_note": { subjective, objective, assessment, plan }
- "highlight_summary": { diagnoses[], symptoms[], medications[], treatment_updates[] }
- "clinical_advice": array of strings

Rules:
- Do not invent missing medical facts.
- If a value is unclear, use "Not clearly stated".
- Keep language professional and medically appropriate.
- Output response in JSON format ONLY — no extra text before or after the JSON.

Medical Document Text:
{$extractedText}

Return ONLY this JSON (include soap_note/highlight_summary/clinical_advice only for medical_form or discharge; omit those keys for all other types):
{
  "document_type": "...",
  "document_label": "...",
  "patient_info": { "name": "...", "dob": "...", "mobile": "..." },
  "short_summary": "...",
  "overall_result": "...",
  "red_flags": [],
  "soap_note": { "subjective": "...", "objective": "...", "assessment": "...", "plan": "..." },
  "highlight_summary": { "diagnoses": [], "symptoms": [], "medications": [], "treatment_updates": [] },
  "clinical_advice": []
}
PROMPT;
    }

    /**
     * Extract and decode JSON from Claude's text response.
     * Handles cases where Claude wraps JSON in markdown code fences.
     */
    protected function parseJsonFromText(string $text): array
    {
        // Strip markdown code fences if present
        $text = preg_replace('/^```(?:json)?\s*/m', '', $text);
        $text = preg_replace('/```\s*$/m', '', $text);
        $text = trim($text);

        // Find first { and last } to isolate the JSON object
        $start = strpos($text, '{');
        $end   = strrpos($text, '}');

        if ($start !== false && $end !== false) {
            $text = substr($text, $start, $end - $start + 1);
        }

        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to parse Claude JSON response: ' . json_last_error_msg() . ' | Raw: ' . $text);
        }

        return $decoded;
    }
}
