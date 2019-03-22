<?php

namespace Bread\Http\Controllers;

use Bread\BreadFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ManagerController extends Controller
{
    public function index()
    {
        return view('bread::manager.browse');
    }

    // Create AND edit a BREAD for a table
    public function create($table)
    {
        return view('bread::manager.edit-add', compact('table'));
    }

    public function store(Request $request)
    {
        // Todo: Remove lists and views prop from formfields
        $json = @json_decode($request->bread);
        if (json_last_error() == JSON_ERROR_NONE) {
            $path = BreadFacade::breadPath().$json->table.'.json';
            File::put($path, json_encode($json, JSON_PRETTY_PRINT));
            return response()->json(['path' => $json->table.'.json']);
        }

        BreadFacade::debug('Saving BREAD failed: '.json_last_error());

        return response('Invalid JSON');
    }

    public function destroy($table)
    {
    }
}
