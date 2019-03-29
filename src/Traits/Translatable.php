<?php

namespace Bread\Traits;

use Bread\BreadFacade;

trait Translatable
{
    public function getTranslation($field, $locale = null)
    {
        $locale = $locale ?? BreadFacade::getLocale();
        $translation = $field;
        $prop = $this->{$field};

        if ($prop) {
            if (is_object($prop)) {
                $translation = $this->getTranslationFromObject($prop, $locale);
            } elseif (is_array($prop)) {
                $translation = $this->getTranslationFromArray($prop, $locale);
            } else {
                $translation = $this->getTranslationFromString($prop, $locale);
            }
        }

        return $translation;
    }

    public function getTranslationFromObject(object $object, $locale = null)
    {
        $locale = $locale ?? BreadFacade::getLocale();

        return $object->{$locale} ?? '';
    }

    public function getTranslationFromArray(array $array, $locale = null)
    {
        $locale = $locale ?? BreadFacade::getLocale();

        return $array[$locale] ?? '';
    }

    public function getTranslationFromString(string $string, $locale = null)
    {
        $locale = $locale ?? BreadFacade::getLocale();

        $json = @json_decode($string);
        if (json_last_error() == JSON_ERROR_NONE) {
            return $json->{$locale} ?? '';
        } else {
            return $string;
        }
    }
}
