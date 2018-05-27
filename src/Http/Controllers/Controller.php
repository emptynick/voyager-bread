<?php

namespace Bread\Http\Controllers;

use Illuminate\Http\Request;

class Controller
{
    public function getSlug(Request $request)
    {
        return explode('.', $request->route()->getName())[1];
    }

    public function getRelationships($bread)
    {
        return collect(['rl1']);
    }

    public function getAttributes($bread)
    {
        return collect(['ble']);
    }
}
