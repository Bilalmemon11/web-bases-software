<?php

if (!function_exists('format_currency_unit')) {
    /**
     * Convert numeric rupee amount into Cr/Lac/Thousands format
     * Example: 12500000 -> 1.25 Cr
     */
    function format_currency_unit($amount)
    {
        if ($amount >= 10000000) {
            return round($amount / 10000000, 2) . ' Cr';
        } elseif ($amount >= 100000) {
            return round($amount / 100000, 2) . ' Lac';
        } elseif ($amount >= 1000) {
            return round($amount / 1000, 2) . ' K';
        } else {
            return number_format($amount);
        }
    }
}
