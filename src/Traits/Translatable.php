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

    public function __get($field)
    {
        return $this->getTranslation($field, app()->getLocale(), config('app.fallback_locale', 'en'));
    }

    public function __set($field, $value)
    {
        return $this->setTranslation($field, $value, app()->getLocale());
    }

    public function getTranslation($field, $locale, $fallback)
    {
        if ($this->translatable && in_array($field, $this->translatable)) {
            $value = $this->getAttribute($field);
            if ($value) {
                //Todo: Check if attribute is casted (already)
                $json = json_decode($value);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = collect($json);

                    if ($value->has($locale)) {
                        return $value->get($locale);
                    } elseif ($value->has($fallback)) {
                        return $value->get($fallback);
                    }
                } else {
                    // Value is not valid JSON, just return value
                    return $value;
                }
            }

            return '';
        }

        return $this->getAttribute($field);
    }

    public function setTranslation($field, $value, $locale)
    {
        if ($this->translatable && in_array($field, $this->translatable)) {
            $initial = $this->getAttribute($field);
            if ($initial = '') {
                $initial = null;
            }
            $json = json_decode($initial);
            //if the original value is a string and $locale is different from app-locale,
            //the original value would be discarded
            if (!$json && $locale != app()->getLocale()) {
                $json = new StdClass();
                $json->{app()->getLoale()} = $initial;
            }
            //Todo: $initial could be a casted JSON object already
            $trans_value = collect($json ?? '{}');
            $trans_value->put($locale, $value);

            return $this->setAttribute($field, json_encode($trans_value));
        }

        return $this->setAttribute($field, $value);
    }
}
