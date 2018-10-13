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
                $value = collect(json_decode($value));
                $locale = app()->getLocale();
                $fallback = config('app.fallback_locale', 'en');
                if ($value->has($locale)) {
                    return $value->get($locale);
                } elseif ($value->has($fallback)) {
                    return $value->get($fallback);
                } else {
                    return '';
                }
            }
        }

        return $this->getAttribute($key);
    }
}
