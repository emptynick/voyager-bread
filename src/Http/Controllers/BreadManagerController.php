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

    //View-Builder
    public function views($table)
    {
        $bread = BreadFacade::getBread($table);
        $fields = SchemaManager::describeTable($table)->keys();

        $breakpoints = collect(config('bread.views.breakpoints'))->sortByDesc('width');

        $bp_widths = $breakpoints->map(function ($bp) {
            return $bp['width'];
        });

        $bp_cols = $breakpoints->map(function ($bp) {
            return $bp['columns'];
        });

        return view('bread::view', [
            'views'       => $bread->layouts->where('type', 'view'),
            'bread'       => $bread,
            'fields'      => $fields,
            'table'       => $table,
            'editing'     => true,
            'breakpoints' => $breakpoints,
            'highest_bp'  => $breakpoints->keys()->first(),
            'bp_cols'     => $bp_cols,
            'bp_widths'   => $bp_widths,
        ]);
    }

    //Store Views
    public function storeViews(Request $request, $table)
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

    public function lists($table)
    {

    }

    public function storeLists(Request $request, $table)
    {

    }
}
