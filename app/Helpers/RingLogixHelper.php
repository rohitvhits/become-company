<?php

namespace App\Helpers;

class RingLogixHelper
{
    public static function callTypeLabel($type): string
    {
        $types = [
            0 => 'Outbound',
            1 => 'Inbound',
            2 => 'Missed',
        ];

        return $types[(int) $type] ?? 'Unknown';
    }

    public static function normalizePhone(?string $phone): string
    {
        return preg_replace('/\D+/', '', (string) $phone);
    }

    public static function extractSipUser(?string $sipUri): string
    {
        if (!$sipUri) return '';
        return preg_replace('/^sip:|@.*$/i', '', $sipUri);
    }

    public static function isNumberInCdr(array $cdr, ?string $phone): bool
    {
        $needle = self::normalizePhone($phone);

        if ($needle === '') {
            return true;
        }

        $cdrR = is_array($cdr['CdrR'] ?? null) ? $cdr['CdrR'] : [];

        $candidates = [
            self::extractSipUser($cdr['orig_from_uri'] ?? ''),
            $cdr['orig_req_user'] ?? '',
            $cdr['orig_to_user'] ?? '',
            $cdrR['orig_from_user'] ?? '',
            $cdrR['orig_req_user'] ?? '',
            $cdrR['orig_to_user'] ?? '',
            $cdrR['orig_id'] ?? '',
            $cdrR['term_id'] ?? '',
        ];

        foreach ($candidates as $candidate) {
            $normalized = self::normalizePhone($candidate);
            if ($normalized !== '' && str_contains($normalized, $needle)) {
                return true;
            }
        }

        return false;
    }
}
