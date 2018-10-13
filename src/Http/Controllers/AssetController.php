<?php

namespace Bread\Http\Controllers;

use Bread\BreadFacade;
use Illuminate\Http\Request;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Facades\Voyager;

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
