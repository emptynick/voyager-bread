<?php

namespace Bread\Traits;

use Bread\BreadFacade;

trait Translatable
{
    public function getTranslation($field)
    {
        if (is_object($field)) {
            return $field->{BreadFacade::getLocale()};
        } elseif (is_object($this->{$field})) {
            return $this->{$field}->{BreadFacade::getLocale()};
        }

        return $this->{$field};
    }
}
