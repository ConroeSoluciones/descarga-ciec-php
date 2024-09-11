<?php

declare(strict_types=1);

if (!function_exists('array_keys_exist')) {
    /**
     * Checks if multiple keys exist in an array
     * @phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint
     * @phpstan-ignore-next-line
     */
    function array_keys_exist(array $array, array $keys): bool
    {
        return count(array_intersect_key(array_flip($keys), $array)) === count($array);
    }
}

if (!function_exists('is_list')) {
    /**
     * @phpstan-ignore-next-line
     */
    function is_list(array $arr): bool
    {
        /**
         * Checks if the given array is a list (numeric key array)
         * @phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint
         */
        return !(array_keys($arr) !== range(0, count($arr) - 1));
    }
}
