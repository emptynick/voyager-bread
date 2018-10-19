<?php

if (!function_exists('translate_elements')) {
    function translate_elements($input)
    {
        foreach ($input as $key => $element) {
            if (is_array($element) || is_object($element)) {
                if (is_array($input)) {
                    $input[$key] = translate_elements($element);
                } elseif (is_object($input)) {
                    $input->{$key} = translate_elements($element);
                }
            } elseif (is_string($element) && starts_with($element, '__')) {
                $value = __(str_after($element, '__'));
                if (is_array($input) || get_class($input) == 'Illuminate\Support\Collection') {
                    $input[$key] = $value;
                } elseif (is_object($input)) {
                    $input->{$key} = $value;
                }
            }
        }
        return $input;
    }
}

if (!function_exists('get_translated_value')) {
    function get_translated_value($input)
    {
        $data = collect(json_decode($input));
        if (json_last_error() == JSON_ERROR_NONE) {
            if ($data->has(app()->getLocale())) {
                return $data->get(app()->getLocale());
            } elseif ($data->has(config('app.fallback_locale'))) {
                return $data->get(config('app.fallback_locale'));
            } else {
                return $data->first();
            }
        }
        return $input;
    }
}

if (!function_exists('get_translated_values')) {
    function get_translated_values($input)
    {
        $data = collect(json_decode($input));
        if (json_last_error() == JSON_ERROR_NONE) {
            return $data;
        }
        return $input;
    }
}
