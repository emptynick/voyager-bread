<?php

namespace Bread\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BreadController extends Controller
{
    // Browse
    public function index()
    {
        if ($this->bread && $layout = $this->bread->getLayout('browse')) {
            $bread = $this->bread;
            unset($bread->layouts);
            return view('bread::bread.browse')->with([
                'bread'  => $bread,
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

    public function data(Request $request)
    {
        $records = 0;
        $rows = [];

        if ($this->bread) {
            $query = $this->bread->getModel()->select('*');

            // Total records
            $records = $query->count();

            $orderBy = $request->sort['field'];
            $orderDirection = $request->sort['type'] ?? 'asc';

            $page = $request->page ?? 1;
            $perPage = $request->perPage ?? 10;

            $columns = $request->columns ?? [];

            // Load relationships
            foreach ($columns as $column) {
                if (Str::contains($column['field'], '.')) {
                    list($relationship, $field) = explode('.', $column['field']);
                    $query = $query->with($relationship);
                }
            }

            // Searching
            $filters = $request->columnFilters ?? [];
            foreach ($filters as $field => $filter) {
                if (Str::contains($field, '.')) {
                    // Query relationship
                    list($relationship, $field) = explode('.', $field);
                    $query = $query->whereHas($relationship, function ($rquery) use ($field, $filter) {
                        $rquery->where($field, 'like', '%'.$filter.'%');
                    });
                } else {
                    // Normal field search
                    $query = $query->where($field, 'like', '%'.$filter.'%');
                    // Todo: what if field is an accessor?
                }
            }

            // Pagination
            $query = $query->get()->slice(($page - 1) * $perPage)->take($perPage);

            // Sorting
            if ($orderDirection == 'desc') {
                $query = $query->sortByDesc($orderBy);
            } else {
                $query = $query->sortBy($orderBy);
            }

            $rows = $query->values()->toArray();
        }

        return response()->json([
            'records' => $records,
            'rows'    => $rows
        ]);
    }
}
