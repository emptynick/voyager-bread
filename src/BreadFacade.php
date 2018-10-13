<?php

namespace Bread;

use Illuminate\Support\Facades\Facade;

class BreadFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Bread::class;
    }
}
