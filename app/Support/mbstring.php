<?php

// Polyfill for mb_split when ext-mbstring is not available.
if (!function_exists('mb_split')) {
    /**
     * Split multibyte string by a regular expression pattern.
     *
     * @param string $pattern
     * @param string $string
     * @param int $limit
     * @return array
     */
    function mb_split(string $pattern, string $string, int $limit = -1): array
    {
        $delimiter = '/' . str_replace('/', '\/', $pattern) . '/u';
        $splitLimit = $limit === 0 ? -1 : $limit;

        return preg_split($delimiter, $string, $splitLimit, PREG_SPLIT_NO_EMPTY) ?: [];
    }
}
