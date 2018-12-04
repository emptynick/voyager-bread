<?php

if (!function_exists('get_translated_value')) {
    function get_translated_value($input, $locale = null)
    {
        $input = collect($input);
        $locale = $locale ?? app()->getLocale();
        $fallback = config('app.fallback_locale');
        if ($input->has($locale)) {
            return $input->get($locale);
        } elseif ($input->has($fallback)) {
            return $input->get($fallback);
        }

        return $input->first();
    }
}
