<?php

namespace Bread\Http\Controllers;

use Bread\BreadFacade;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;

    protected $bread;

    public function __construct(Request $request)
    {
        $this->bread = BreadFacade::getBreadBySlug(explode('.', $request->route()->getName())[1]);
    }
}
