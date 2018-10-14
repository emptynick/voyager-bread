<?php

namespace Bread\Traits;

trait Translatable
{
    public function __construct()
    {
        if ($this->translatable && is_array($this->translatable) && count($this->translatable) > 0) {
            $this->isTranslatable = true;
        } else {
            $this->isTranslatable = false;
        }
    }

    public function isFieldTranslatable($field)
    {
        return $this->translatable && in_array($field, $this->translatable);
    }

    public function __get($key)
    {
        if ($this->translatable && in_array($key, $this->translatable)) {
            $value = $this->getAttribute($key);
            if ($value) {
                //Todo: Check if attribute is casted (already)
                $json = json_decode($value);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = collect($json);
                    $locale = app()->getLocale();
                    $fallback = config('app.fallback_locale', 'en');

                    if ($value->has($locale)) {
                        return $value->get($locale);
                    } elseif ($value->has($fallback)) {
                        return $value->get($fallback);
                    }
                } else {
                    // Value is not valid JSON, just return value
                    return $value;
                }

                return '';
            }
        }

        return $this->getAttribute($key);
    }
}
