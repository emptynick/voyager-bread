<?php

namespace Bread\Http\Controllers;

use Bread\BreadFacade;
use Bread\Classes\Bread;
use Illuminate\Http\Request;
use TCG\Voyager\Database\Schema\SchemaManager;

class ManagerController extends Controller
{
    public function index()
    {
        $tables = array_diff(
            SchemaManager::listTableNames(),
            config('voyager.database.tables.hidden', [])
        );

        return view('bread::manager.browse', [
            'tables' => $tables,
            'breads' => BreadFacade::getBreads(),
        ]);
    }

    public function create($table)
    {
        return view('bread::manager.edit-add', [
            'bread' => json_encode(new Bread([
                'table'                 => $table,
                'display_name_singular' => title_case(str_singular($table)),
                'display_name_plural'   => title_case($table),
                'slug'                  => str_slug($table),
                'model_name'            => 'App\\'.title_case(str_singular($table)),
            ])),
            'table'  => $table,
            'fields' => SchemaManager::describeTable($table)->keys(),
        ]);
    }

    public function store(Request $request)
    {
        \Cache::forget('breads');
        if (BreadFacade::saveBread(json_decode($request->bread))) {
            return response()->json('', 200);
        }

        return response()->json('', 500);
    }

    public function edit($table)
    {
        $bread = BreadFacade::getBreadByTable($table);
        if ($bread) {
            $model = null;
            if (class_exists($bread->model_name)) {
                $model = app($bread->model_name);
            } else {
                \Session::flash('message', 'The model for this BREAD does not exist. You can not select relationships or accessors!');
                \Session::flash('alert-type', 'error');
            }

            return view('bread::manager.edit-add', [
                'bread'         => json_encode($bread),
                'table'         => null,
                'fields'        => SchemaManager::describeTable($bread->table)->keys(),
                'accessors'     => $this->getAccessors($model),
                'relationships' => $this->getRelationships($model, true),
            ]);
        }

        return redirect()->route('voyager.bread.index')
        ->with([
            'message'    => 'BREAD for table '.$table.' does not exist!',
            'alert-type' => 'error',
        ]);
    }

    public function destroy($table)
    {
        \Cache::forget('breads');
        if (!BreadFacade::deleteBread($table)) {
            return response()->json([], 500);
        }

        return response()->json([], 200);
    }
}
