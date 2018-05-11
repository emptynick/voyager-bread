<?php

namespace Bread\Http\Controllers;

use Bread\BreadFacade;
use Bread\Classes\Layout;
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
        $bread = collect(BreadFacade::getBread($request->table_name));
        $bread->merge($request->except('_token'));

        BreadFacade::saveBread($request->table_name, $bread);

        return redirect()
                ->route('voyager.bread.edit', ['table' => $request->table_name])
                ->with([
                    'message'    => __('voyager::generic.successfully_updated').' '.$request->display_name_singular,
                    'alert-type' => 'success',
                ]);
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
        $name = $layout;
        $bread = BreadFacade::getBread($table);
        $layout = $bread->getLayout($layout);
        $fields = SchemaManager::describeTable($table)->keys();

        if ($layout->type == 'view') {
            return view('bread::view', ['view' => $layout, 'bread' => $bread, 'fields' => $fields, 'table' => $table, 'name' => $name]);
        } elseif ($layout->type == 'list') {
            return view('bread::list', ['list' => $layout, 'bread' => $bread, 'fields' => $fields]);
        }
    }

    //Store Layout
    public function storeLayout(Request $request, $table, $name)
    {
        $layout = json_decode($request->input('content'));

        $bread = collect(BreadFacade::getBread($table));
        $bread['layouts'] = $bread['layouts']->where('name', '!=', $name);

        $new_layout = new Layout($layout);

        if ($new_layout->validate()) {
            $bread['layouts'][] = $new_layout;
        }

        BreadFacade::saveBread($table, $bread);

        return redirect()
                ->route('voyager.bread.edit.layout', ['table' => $table, 'name' => $name])
                ->with([
                    'message'    => __('voyager::generic.successfully_updated'),
                    'alert-type' => 'success',
                ]);
    }
}
