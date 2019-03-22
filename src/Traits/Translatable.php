<?php

namespace Bread\Traits;

use Bread\BreadFacade;

trait Translatable
{
    public function getTranslation($field, $locale = null)
    {
        $locale = $locale ?? BreadFacade::getLocale();
        if (is_object($field)) {
            return $field->{$locale};
        } elseif (is_object($this->{$field})) {
            return $this->{$field}->{$locale};
        }

        return $this->{$field};
    }
}
