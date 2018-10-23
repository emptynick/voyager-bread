<?php

namespace Bread\Http\Controllers;

use Bread\BreadFacade;
use Illuminate\Http\Request;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Facades\Voyager;

class ManagerController extends Controller
{
    public function index()
    {
        $tables = array_diff(
            SchemaManager::listTableNames(),
            config('voyager.database.tables.hidden', [])
        );

        return view('bread::manager.index', compact('tables'));
    }

    public function create($table)
    {
        return view('bread::manager.edit-add', compact('table'));
    }

    public function store(Request $request)
    {
        $bread = BreadFacade::getBreadByTable($request->table_name);
        if (!$bread) {
            $bread = new \Bread\Classes\Bread([]);
        }
        foreach ($request->except('_token') as $key => $value) {
            $bread->{$key} = $value;
        }

        BreadFacade::saveBread($request->table_name, $bread);

        return redirect()
                ->route('voyager.bread.edit', ['table' => $request->table_name])
                ->with([
                    'message'    => __('voyager::generic.successfully_updated').' '.get_translated_value($request->display_name_singular),
                    'alert-type' => 'success',
                ]);
    }

    public function edit($table)
    {
        $bread = BreadFacade::getBreadByTable($table);
        if ($bread) {
            return view('bread::manager.edit-add', compact('bread', 'table'));
        }

        return redirect()->route('voyager.bread.index')->with([
            'message'    => __('bread::manager.bread_not_found', ['bread' => $table]),
            'alert-type' => 'error',
        ]);
    }

    public function destroy($table)
    {
        if (BreadFacade::deleteBread($table)) {
            return redirect()->route('voyager.bread.index')->with([
                'message'    => __('bread::manager.bread_deleted', ['bread' => $table]),
                'alert-type' => 'success',
            ]);
        } else {
            return redirect()->route('voyager.bread.index')->with([
                'message'    => __('bread::manager.bread_not_deleted', ['bread' => $table]),
                'alert-type' => 'error',
            ]);
        }
    }

    public function views($table, $name = '')
    {
        $bread = BreadFacade::getBreadByTable($table);
        if ($bread) {
            $fields = SchemaManager::describeTable($table)->keys()->merge($this->getAccessors($bread));

            return view('bread::manager.views', [
                'views'         => $bread->getViews()->values(),
                'bread'         => $bread,
                'fields'        => $fields,
                'relationships' => $this->getRelationships($bread),
                'table'         => $table,
                'model'         => app($bread->model),
            ]);
        }

        return redirect()->route('voyager.bread.index')->with([
            'message'    => __('bread::manager.bread_not_found', ['bread' => $table]),
            'alert-type' => 'error',
        ]);
    }

    public function lists($table, $name = '')
    {
        $bread = BreadFacade::getBreadByTable($table);

        if ($bread) {
            $fields = SchemaManager::describeTable($table)->keys()->merge($this->getAccessors($bread));

            return view('bread::manager.lists', [
                'lists'         => $bread->getLists()->values(),
                'bread'         => $bread,
                'fields'        => $fields,
                'relationships' => $this->getRelationships($bread),
                'table'         => $table,
                'model'         => app($bread->model),
            ]);
        }

        return redirect()->route('voyager.bread.index')->with([
            'message'    => __('bread::manager.bread_not_found', ['bread' => $table]),
            'alert-type' => 'error',
        ]);
    }

    public function storeLayouts(Request $request, $table)
    {
        $bread = BreadFacade::getBreadByTable($table);

        if ($bread) {
            if ($request->has('views')) {
                $views = json_decode($request->input('views'));
                $bread->layouts = $bread->layouts->where('type', '!=', 'view')->merge($views);
            }
            if ($request->has('lists')) {
                $lists = json_decode($request->input('lists'));
                $bread->layouts = $bread->layouts->where('type', '!=', 'list')->merge($lists);
            }

            BreadFacade::saveBread($table, $bread);
        }
    }
}
