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
        } elseif (property_exists($this, $field) && is_object($this->{$field})) {
            return $this->{$field}->{$locale};
        }

        // Todo: this was uncommented
        //return $this->{$field};
        return $field;
    }
}
