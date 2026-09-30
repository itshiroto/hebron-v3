<?php

namespace App\Support;

class ServiceTimes
{
    /**
     * @return array<int, array{title: string, time: string, livestream: bool, note: string}>
     */
    public static function normalize(mixed $services): array
    {
        if (! is_array($services)) {
            return [];
        }

        $out = [];
        foreach ($services as $service) {
            $time = is_array($service) ? trim((string) ($service['time'] ?? '')) : '';
            if ($time === '') {
                continue;
            }
            $out[] = [
                'title' => trim((string) ($service['title'] ?? '')),
                'time' => trim(preg_replace('/\s*WIB\s*$/i', '', $time)),
                'livestream' => (bool) ($service['livestream'] ?? false),
                'note' => trim((string) ($service['note'] ?? '')),
            ];
        }

        return $out;
    }
}
