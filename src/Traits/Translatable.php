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
            return $this->getTranslationFromObject($field, $locale);
        } elseif (is_array($field)) {
            return $this->getTranslationFromArray($field, $locale);
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

    public function getTranslationFromObject(object $object, $locale)
    {
        return $object->{$locale} ?? '';
    }

    public function getTranslationFromArray(array $array, $locale)
    {
        return $array[$locale] ?? '';
    }
}
