<?php

namespace Bread\Http\Controllers;

use Illuminate\Http\Request;

class Controller
{
    public function getSlug(Request $request)
    {
        return explode('.', $request->route()->getName())[1];
    }
}
