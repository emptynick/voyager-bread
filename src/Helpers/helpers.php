<?php

if (!function_exists('get_translated_value')) {
    function get_translated_value($input, $locale = null) {
        $input = collect($input);
        $locale = $locale ?? app()->getLocale();
        if ($input->has($locale)) {
            return $input->get($locale);
        }

        return $input->first();
    }
}
