<?php

namespace Bread\Http\Controllers;

use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function index()
    {
        return view('bread::manager.browse');
    }

    public function create($table)
    {
    }

    public function store(Request $request)
    {
    }

    public function edit($table)
    {
    }

    public function destroy($table)
    {
    }
}
