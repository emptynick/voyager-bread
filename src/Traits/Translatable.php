<?php

namespace Bread\Traits;

use Bread\BreadFacade;

trait Translatable
{
    public function getTranslation($field, $locale = null)
    {
        $locale = $locale ?? BreadFacade::getLocale();

        if (is_object($field)) {
            return $field->{$locale} ?? '';
        } elseif (is_array($field)) {
            return $field[$locale] ?? '';
        } elseif (is_int($field)) {
            return $field;
        } elseif ($this->{$field} && is_object($this->{$field})) {
            return $this->{$field}->{$locale} ?? '';
        } elseif ($this->{$field} && is_array($this->{$field})) {
            return $this->{$field}[$locale] ?? '';
        } elseif ($this->{$field}) {
            $json = @json_decode($this->{$field});
            if (json_last_error() == JSON_ERROR_NONE) {
                return $json->{$locale} ?? '';
            } else {
                return $this->{$field};
            }
        }

        return $field;
    }
}
