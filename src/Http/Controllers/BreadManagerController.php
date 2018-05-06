<?php

namespace Bread\Http\Controllers;

use Bread\BreadFacade;
use Bread\Models\BreadRow;
use Illuminate\Http\Request;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Facades\Voyager;

class BreadManagerController extends Controller
{
    public function index()
    {
        $tables = array_diff(
            SchemaManager::listTableNames(),
            config('voyager.database.tables.hidden', [])
        );

        return view('bread::manager.index', compact('tables'));
    }

    //Create BREAD
    public function create($table)
    {
        return view('bread::manager.edit-add', compact('table'));
    }

    //Store created or updates BREAD
    public function store(Request $request)
    {

    }

    //Edit BREAD
    public function edit($table)
    {
        $bread = BreadFacade::getBread($table);
        return view('bread::manager.edit-add', compact('bread', 'table'));
    }

    //Delete BREAD
    public function destroy(Request $request, $bread)
    {

    }

    //Edit Layout
    public function editLayout($table, $layout)
    {
        $bread = BreadFacade::getBread($table);
        $layout = $bread->getLayout($layout);
        
        if ($layout->type == 'view') {
            return view('bread::manager.edit-view', ['view' => $layout, 'bread' => $bread]);
        }
        elseif ($layout->type == 'list') {
            return view('bread::manager.edit-list', ['list' => $layout, 'bread' => $bread]);
        }
    }
}
