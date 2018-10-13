<?php

namespace Bread\Http\Controllers;

class AssetController extends Controller
{
    public function styles()
    {
        return response(
            file_get_contents(__DIR__.'/../../../publishable/assets/css/styles.css'),
            200,
            ['Content-Type' => 'text/css']
        );
    }

    public function scripts()
    {
        return response(
            file_get_contents(__DIR__.'/../../../publishable/assets/js/scripts.js'),
            200,
            ['Content-Type' => 'application/javascript']
        );
    }
}
