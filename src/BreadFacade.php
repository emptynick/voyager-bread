<?php

namespace Bread;

use Illuminate\Support\Facades\Facade;

class BreadFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Bread::class;
    }
}
