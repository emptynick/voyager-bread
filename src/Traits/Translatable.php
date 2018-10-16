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

    public function getAttributeValue($key)
    {
        if (!$this->translatable || !$this->isFieldTranslatable($key)) {
            return parent::getAttributeValue($key);
        }

        return $this->getTranslation($key, app()->getLocale());
    }

    public function setAttribute($key, $value)
    {
        if (!$this->translatable || !$this->isFieldTranslatable($key)) {
            return parent::setAttribute($key, $value);
        }

        return $this->setTranslation($key, $this->getLocale(), $value);
    }

    public function getTranslation($key, $locale)
    {
        $trans = $this->getTranslations($key);
        if (gettype($trans) != 'array') {
            return $this->getAttributes()[$key];
        }
        $translation = $trans[$locale] ?? $trans[config('app.fallback_locale')] ?? '';

        return $translation;
    }

    public function getTranslations($key)
    {
        return json_decode($this->getAttributes()[$key] ?? '' ?: '{}', true);
    }

    public function setTranslation($key, $value, $locale)
    {
        $translations = $this->getTranslations($key);
        $translations[$locale] = $value;
        $this->{$key} = $this->asJson($translations);

        return $this;
    }
}
