<?php

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

if (!function_exists('get_translated_value_recursive')) {
    function get_translated_value_recursive($input)
    {
        if (is_string($input)) {
            return get_translated_value($input);
        } elseif (is_array($input)) {
            $data = [];
            foreach ($input as $key => $value) {
                $data[$key] = get_translated_value_recursive($value);
            }

            return $data;
        }
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
