<?php

namespace Bread\Traits;

use Bread\BreadFacade;

trait Translatable
{
    public function getTranslation($field, $locale = null)
    {
        $locale = $locale ?? BreadFacade::getLocale();
        $translation = $field;
        if (is_object($field)) {
            $translation = $field->{$locale} ?? '';
        } elseif (is_array($field)) {
            $translation = $field[$locale] ?? '';
        } elseif (is_int($field)) {
            $translation = $field;
        } elseif ($this->{$field} && is_object($this->{$field})) {
            $translation = $this->{$field}->{$locale} ?? '';
        } elseif ($this->{$field} && is_array($this->{$field})) {
            $translation = $this->{$field}[$locale] ?? '';
        } elseif ($this->{$field}) {
            $json = @json_decode($this->{$field});
            if (json_last_error() == JSON_ERROR_NONE) {
                $translation = $json->{$locale} ?? '';
            } else {
                $translation = $this->{$field};
            }
        }

        return $translation;
    }
}
