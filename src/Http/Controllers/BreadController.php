<?php

namespace Bread\Http\Controllers;

use Illuminate\Http\Request;

class BreadController extends Controller
{
    // Browse
    public function index()
    {
        if ($this->bread && $layout = $this->bread->getLayout('browse')) {
            return view('bread::bread.browse')->with([
                'bread'  => $this->bread,
                'layout' => $layout,
            ]);
        }
    }

    // Add
    public function create()
    {
        if ($this->bread && $layout = $this->bread->getLayout('add')) {
            return view('bread::bread.edit-add')->with([
                'bread'   => $this->bread,
                'layouts' => $layout,
            ]);
        }
    }

    public function store(Request $request, $redirect = true)
    {
    }

    // Read
    public function show($id)
    {
        if ($this->bread && $layout = $this->bread->getLayout('read')) {
            return view('bread::bread.read')->with([
                'bread'   => $this->bread,
                'layouts' => $layout,
            ]);
        }
    }

    // Edit
    public function edit($id)
    {
        if ($this->bread && $layout = $this->bread->getLayout('edit')) {
            return view('bread::bread.edit-add')->with([
                'bread'   => $this->bread,
                'layouts' => $layout,
            ]);
        }
    }

    public function update(Request $request, $id)
    {
    }

    // Delete
    public function destroy(Request $request, $id)
    {
    }
}
