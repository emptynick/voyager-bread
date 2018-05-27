<?php

/**
 * Gets all fields of a model.
 *
 * @param Illuminate\Database\Eloquent\Model $relationship The relationship
 *
 * @return Illuminate\Support\Collection The columns
 */
if (!function_exists('config_get_colors')) {
    function config_get_colors()
    {
        $colors = config('bread.colors', 'basic');
        if (is_array($colors)) {
            return str_replace('"', "'", json_encode($colors));
        } else {
            return '\''.$colors.'\'';
        }
    }
}
