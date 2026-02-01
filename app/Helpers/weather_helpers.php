<?php
if (!function_exists('ow_icon_to_fa')) {
    function ow_icon_to_fa(string $icon): string
    {
        $map = [
            '01d' => 'fas fa-sun',
            '01n' => 'fas fa-moon',
            '02d' => 'fas fa-cloud-sun',
            '02n' => 'fas fa-cloud-moon',
            '03d' => 'fas fa-cloud',
            '03n' => 'fas fa-cloud',
            '04d' => 'fas fa-cloud',
            '04n' => 'fas fa-cloud',
            '09d' => 'fas fa-cloud-showers-heavy',
            '09n' => 'fas fa-cloud-showers-heavy',
            '10d' => 'fas fa-cloud-rain',
            '10n' => 'fas fa-cloud-rain',
            '11d' => 'fas fa-cloud-bolt',
            '11n' => 'fas fa-cloud-bolt',
            '13d' => 'fas fa-snowflake',
            '13n' => 'fas fa-snowflake',
            '50d' => 'fas fa-smog',
            '50n' => 'fas fa-smog',
        ];

        return $map[$icon] ?? 'fas fa-question-circle';
    }
}

if (!function_exists('ow_icon_color')) {
    function ow_icon_color(string $icon): string
    {
        $colors = [
            '01d' => '#FFCC33',
            '01n' => '#6B7280',
            '02d' => '#FFD872',
            '02n' => '#94A3B8',
            '03d' => '#dfe3e9',
            '03n' => '#6B7280',
            '04d' => '#94A3B8',
            '04n' => '#6B7280',
            '09d' => '#3B82F6',
            '09n' => '#2563EB',
            '10d' => '#3B82F6',
            '10n' => '#2563EB',
            '11d' => '#7C3AED',
            '11n' => '#6D28D9',
            '13d' => '#60A5FA',
            '13n' => '#60A5FA',
            '50d' => '#9CA3AF',
            '50n' => '#6B7280',
        ];

        return $colors[$icon] ?? '#9CA3AF';
    }
}
