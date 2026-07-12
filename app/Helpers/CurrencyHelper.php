<?php

if (!function_exists('format_currency')) {
    /**
     * Format an amount to a given currency.
     *
     * @param float|int $amount
     * @param string $currency
     * @return string
     */
    function format_currency($amount, $currency = 'RWF')
    {
        return $currency . ' ' . number_format($amount, 0);
    }
}
