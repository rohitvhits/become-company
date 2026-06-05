<?php

namespace App\Services;

use App\Helpers\RingLogixHelper;
use App\Helpers\Utility;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class RingLogixService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('RINGLOGIX_BASE_URL', 'https://api.ringlogix.com/pbx/v1'), '/');
    }

    public function getAccessToken(bool $forceRefresh = false): string
    {
        $this->ensureConfigured();

        $cacheKey = $this->tokenCacheKey();

        if (!$forceRefresh && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->withoutVerifying()
                ->timeout(30)
                ->post($this->baseUrl . '/oauth2/token/', [
                    'grant_type'    => 'password',
                    'client_id'     => env('RINGLOGIX_CLIENT_ID'),
                    'client_secret' => env('RINGLOGIX_CLIENT_SECRET'),
                    'username'      => env('RINGLOGIX_USERNAME'),
                    'password'      => env('RINGLOGIX_PASSWORD'),
                ])
                ->throw();
        } catch (RequestException $e) {
            throw new RuntimeException('RingLogix authentication failed. Please verify API credentials.');
        }

        $payload = $response->json();
        $token   = $payload['access_token'] ?? null;

        if (!$token) {
            throw new RuntimeException('RingLogix authentication response did not include an access token.');
        }

        $expiresIn = max((int) ($payload['expires_in'] ?? 3600) - 60, 60);
        Cache::put($cacheKey, $token, now()->addSeconds($expiresIn));

        return $token;
    }

    public function getCallDetails(array $filters): array
    {
        // Cache key includes domain, dates, and pagination params so that
        // ajaxList (max_pages=5) and ajaxTable (max_pages=50) don't share the same cache entry.
        $limit    = min(max((int) ($filters['limit'] ?? 500), 1), 500);
        $maxPages = max((int) ($filters['max_pages'] ?? 50), 1);
        $domainCacheKey = 'ringlogix_cdr_domain_' . md5(json_encode([
            'domain'     => $filters['domain'] ?? env('RINGLOGIX_DOMAIN'),
            'start_date' => $filters['start_date'] ?? '',
            'end_date'   => $filters['end_date'] ?? '',
            'limit'      => $limit,
            'max_pages'  => $maxPages,
        ]));

        $records = Cache::remember($domainCacheKey, now()->addMinutes(60), function () use ($filters) {
            $all = $this->getAllCallDetailsByPagination($filters);
            return $this->filterRecordsByDateRange($all, $filters['start_date'], $filters['end_date']);
        });

        $phones = array_values(array_filter((array) ($filters['phone'] ?? []), fn($p) => !empty(trim((string) $p))));
        if (!empty($phones)) {
            $records = array_values(array_filter($records, function ($cdr) use ($phones) {
                foreach ($phones as $phone) {
                    if (RingLogixHelper::isNumberInCdr($cdr, $phone)) {
                        return true;
                    }
                }
                return false;
            }));
        }

        return $records;
    }

    protected function getAllCallDetailsByPagination(array $filters): array
    {
        $limit    = min(max((int) ($filters['limit'] ?? 500), 1), 500);
        $start    = max((int) ($filters['start'] ?? 0), 0);
        $maxPages = max((int) ($filters['max_pages'] ?? 50), 1);
        $token    = $this->getAccessToken();
        $records  = [];
        $seenPageSignatures = [];

        for ($page = 1; $page <= $maxPages; $page++) {
            $pageFilters = array_merge($filters, [
                'limit' => $limit,
                'start' => $start,
            ]);

            $response = $this->postCallDetails($token, $pageFilters);

            if ($this->isExpiredTokenResponse($response)) {
                Cache::forget($this->tokenCacheKey());
                $token    = $this->getAccessToken(true);
                $response = $this->postCallDetails($token, $pageFilters);
            }

            if ($this->isInvalidDomainResponse($response)) {
                throw new RuntimeException('Invalid RingLogix domain. Please verify RINGLOGIX_DOMAIN.');
            }

            if (($response['status'] ?? null) === 0 || isset($response['error'])) {
                throw new RuntimeException($response['error_msg'] ?? $response['error_description'] ?? $response['error'] ?? 'Unable to fetch RingLogix call details.');
            }

            $pageRecords = $this->extractRecords($response);

            if (empty($pageRecords)) {
                break;
            }

            $pageSignature = $this->pageSignature($pageRecords);

            if (isset($seenPageSignatures[$pageSignature])) {
                Log::warning('RingLogix CDR pagination stopped because the API returned a repeated page', [
                    'page'  => $page,
                    'start' => $start,
                    'limit' => $limit,
                ]);

                break;
            }

            $seenPageSignatures[$pageSignature] = true;
            $records = array_merge($records, $pageRecords);

            if (count($pageRecords) < $limit) {
                break;
            }

            $start += $limit;
        }

        return $this->sortRecordsByNewest($this->uniqueRecords($records));
    }

    protected function pageSignature(array $records): string
    {
        $first = $records[0] ?? [];
        $last  = $records[count($records) - 1] ?? [];

        return md5(json_encode([
            'count' => count($records),
            'first' => $first['cdr_id'] ?? $first['time_start'] ?? null,
            'last'  => $last['cdr_id'] ?? $last['time_start'] ?? null,
        ]));
    }

    protected function filterRecordsByDateRange(array $records, string $startDate, string $endDate): array
    {
        $startTimestamp = Utility::convertToTimestamp($startDate);
        $endTimestamp   = Utility::convertToTimestamp($endDate);

        return array_values(array_filter($records, function ($record) use ($startTimestamp, $endTimestamp) {
            if (empty($record['time_start'])) {
                return false;
            }

            $timeStart = (int) $record['time_start'];

            return $timeStart >= $startTimestamp && $timeStart <= $endTimestamp;
        }));
    }

    protected function uniqueRecords(array $records): array
    {
        $unique = [];

        foreach ($records as $index => $record) {
            $key = $record['cdr_id'] ?? $record['id'] ?? null;

            if (!$key) {
                $key = implode('|', [
                    $record['time_start'] ?? '',
                    $record['orig_from_uri'] ?? '',
                    $record['orig_req_user'] ?? '',
                    $record['duration'] ?? '',
                    $index,
                ]);
            }

            $unique[$key] = $record;
        }

        return array_values($unique);
    }

    protected function sortRecordsByNewest(array $records): array
    {
        usort($records, function ($first, $second) {
            return (int) ($second['time_start'] ?? 0) <=> (int) ($first['time_start'] ?? 0);
        });

        return $records;
    }

    public function getCdrOrigCallId(string $cdrId, string $timeStart = ''): ?string
    {
        $token    = $this->getAccessToken();
        $response = $this->postCdrById($token, $cdrId, $timeStart);

        if ($this->isExpiredTokenResponse($response)) {
            Cache::forget($this->tokenCacheKey());
            $token    = $this->getAccessToken(true);
            $response = $this->postCdrById($token, $cdrId, $timeStart);
        }

        $records = $this->extractRecords($response);
        $cdr     = $records[0] ?? [];

        return $cdr['CdrR']['orig_callid'] ?? null;
    }

    protected function postCdrById(string $token, string $cdrId, string $timeStart = ''): array
    {
        // RingLogix requires date range even for ID-based lookup
        if ($timeStart && is_numeric($timeStart)) {
            $dateStr   = Utility::convertTimestampToYMD($timeStart);
            $startDate = Utility::convertYMDTime($dateStr);
            $endDate   = Utility::endOfDay($dateStr);
        } else {
            $startDate = Utility::convertYMDTime(Utility::convertYMD('-30 days'));
            $endDate   = Utility::endOfDay(date('Y-m-d'));
        }

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->withToken($token)
                ->withoutVerifying()
                ->timeout(30)
                ->post($this->baseUrl . '/?object=cdr2&action=read', [
                    'format'     => 'json',
                    'domain'     => env('RINGLOGIX_DOMAIN'),
                    'id'         => $cdrId,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'limit'      => 500,
                ]);

            return $response->json() ?? [
                'status' => $response->status(),
                'error'  => $response->body(),
            ];
        } catch (\Throwable $e) {
            throw new RuntimeException('Unable to connect to RingLogix. Please try again.');
        }
    }

    public function getRecordingUrl(string $origCallId): ?string
    {
        $token    = $this->getAccessToken();
        $response = $this->postRecordingRead($token, $origCallId);

        if ($this->isExpiredTokenResponse($response)) {
            Cache::forget($this->tokenCacheKey());
            $token    = $this->getAccessToken(true);
            $response = $this->postRecordingRead($token, $origCallId);
        }

        // Unwrap array response → take first element
        $data = (isset($response[0]) && is_array($response[0])) ? $response[0] : $response;

        if (($data['status'] ?? null) === 0 || isset($data['error'])) {
            Log::warning('RingLogix recording API error response', ['data' => $data]);
            throw new RuntimeException(($data['error_msg'] ?? '') ?: (($data['error'] ?? '') ?: 'Recording is not available for this call.'));
        }

        return $data['url'] ?? null;
    }

    protected function postRecordingRead(string $token, string $origCallId): array
    {
        try {
            $response = Http::asForm()
                ->acceptJson()
                ->withToken($token)
                ->withoutVerifying()
                ->timeout(30)
                ->post($this->baseUrl . '/?object=recording&action=read', [
                    'domain'      => env('RINGLOGIX_DOMAIN'),
                    'orig_callid' => $origCallId,
                ]);

            return $response->json() ?? [
                'status' => $response->status(),
                'error'  => $response->body(),
            ];
        } catch (\Throwable $e) {
            throw new RuntimeException('Unable to connect to RingLogix. Please try again.');
        }
    }

    public function getMessages(array $filters): array
    {
        $token    = $this->getAccessToken();
        $response = $this->postMessages($token, $filters);

        if ($this->isExpiredTokenResponse($response)) {
            Cache::forget($this->tokenCacheKey());
            $response = $this->postMessages($this->getAccessToken(true), $filters);
        }

        if ($this->isInvalidDomainResponse($response)) {
            throw new RuntimeException('Invalid RingLogix domain. Please verify RINGLOGIX_DOMAIN.');
        }

        if (($response['status'] ?? null) === 0 || isset($response['error'])) {
            throw new RuntimeException($response['error_msg'] ?? $response['error_description'] ?? $response['error'] ?? 'Unable to fetch RingLogix messages.');
        }

        return $this->extractRecords($response);
    }

    protected function postMessages(string $token, array $filters): array
    {
        try {
            $params = [
                'domain'     => $filters['domain'] ?? env('RINGLOGIX_DOMAIN'),
                'start_date' => $filters['start_date'],
                'end_date'   => $filters['end_date'],
                'limit'      => $filters['limit'] ?? 100,
            ];

            if (!empty($filters['user'])) {
                $params['user'] = $filters['user'];
            }

            $response = Http::asForm()
                ->acceptJson()
                ->withToken($token)
                ->withoutVerifying()
                ->timeout(30)
                ->post($this->baseUrl . '/?object=messagesession&action=read', $params);

            return $response->json() ?? [
                'status' => $response->status(),
                'error'  => $response->body(),
            ];
        } catch (\Throwable $e) {
            throw new RuntimeException('Unable to connect to RingLogix. Please try again.');
        }
    }

    protected function postCallDetails(string $token, array $filters): array
    {
        try {
            $response = Http::asForm()
                ->acceptJson()
                ->withToken($token)
                ->withoutVerifying()
                ->timeout(30)
                ->post($this->baseUrl . '/?object=cdr2&action=read', [
                    'format'     => 'json',
                    'domain'     => $filters['domain'] ?? env('RINGLOGIX_DOMAIN'),
                    'start_date' => $filters['start_date'],
                    'end_date'   => $filters['end_date'],
                    'limit'      => $filters['limit'] ?? 100,
                    'start'      => $filters['start'] ?? 0,
                ]);

            return $response->json() ?? [
                'status' => $response->status(),
                'error'  => $response->body(),
            ];
        } catch (\Throwable $e) {
            throw new RuntimeException('Unable to connect to RingLogix. Please try again.');
        }
    }

    protected function extractRecords(array $response): array
    {
        foreach (['data', 'records', 'cdrs', 'result'] as $key) {
            if (isset($response[$key]) && is_array($response[$key])) {
                return array_values($response[$key]);
            }
        }

        if (array_is_list($response)) {
            return $response;
        }

        return [];
    }

    protected function ensureConfigured(): void
    {
        foreach (['RINGLOGIX_CLIENT_ID', 'RINGLOGIX_CLIENT_SECRET', 'RINGLOGIX_USERNAME', 'RINGLOGIX_PASSWORD', 'RINGLOGIX_DOMAIN'] as $key) {
            if (empty(env($key))) {
                throw new RuntimeException('RingLogix API is not configured. Please update your .env file.');
            }
        }
    }

    protected function tokenCacheKey(): string
    {
        return 'ringlogix_access_token_' . md5(env('RINGLOGIX_CLIENT_ID') . '|' . env('RINGLOGIX_USERNAME'));
    }

    protected function isExpiredTokenResponse(array $response): bool
    {
        $message = strtolower(json_encode($response));

        return str_contains($message, 'expired token') || str_contains($message, 'token expired') || str_contains($message, 'invalid_token');
    }

    protected function isInvalidDomainResponse(array $response): bool
    {
        $message = strtolower(json_encode($response));

        return str_contains($message, 'invalid domain') || str_contains($message, 'domain invalid');
    }
}
