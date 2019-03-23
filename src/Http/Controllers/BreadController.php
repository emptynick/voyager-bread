<?php

namespace Bread\Http\Controllers;

use Bread\BreadFacade;
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
            $bread = $this->bread;
            unset($bread->layouts);
            $data = $bread->getModel()->findOrFail($id);

            return view('bread::bread.read')->with([
                'bread'  => $bread,
                'layout' => $layout,
                'data'   => $data,
            ]);
        }
    }

    // Edit
    public function edit($id)
    {
        if ($this->bread && $layout = $this->bread->getLayout('edit')) {
            $bread = $this->bread;
            unset($bread->layouts);
            $data = $bread->getModel()->findOrFail($id);
            $url = route('voyager.'.$bread->getTranslation('slug').'.update', $id);

            return view('bread::bread.edit-add')->with([
                'bread'  => $bread,
                'layout' => $layout,
                'data'   => $data,
                'url'    => $url,
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        if ($this->bread && $layout = $this->bread->getLayout('edit')) {
            $bread = $this->bread;
            $validator = $this->getValidator($request, $layout->formfields);

            //dd($validator);
            $validator->validate();
        }
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

            $columns = collect($request->columns ?? []);

            $locale = $request->locale ?? BreadFacade::getLocale();

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
                if ($filter) {
                    if (Str::contains($field, '.')) {
                        // Query relationship
                        list($relationship, $field) = explode('.', $field);
                        $query = $query->whereHas($relationship, function ($rquery) use ($field, $filter) {
                            $rquery->where($field, 'like', '%'.$filter.'%');
                        });
                    } else {
                        // Translatable field
                        if (
                            ($columns->where('field', $field)->first()['options']['translatable'] ?? false) &&
                            ($columns->where('field', $field)->first()['options']['search_in_locale'] ?? false)
                        ) {
                            $query = $query->whereRaw('lower('.$field.'->"$.'.$locale.'") like lower(?)', ["%{$filter}%"]);
                        } else {
                            // Normal field search
                            $query = $query->where($field, 'like', '%'.$filter.'%');
                        }
                    }
                }
            }

            // Sorting
            if ($orderDirection == 'desc') {
                $query = $query->get()->sortByDesc(function ($item) use ($columns, $orderBy, $locale) {
                    if ($columns->where('field', $orderBy)->first()['options']['translatable'] ?? false) {
                        return $item->getTranslation($orderBy, $locale);
                    } else {
                        return $item;
                    }
                });
            } else {
                $query = $query->get()->sortBy(function ($item) use ($columns, $orderBy, $locale) {
                    if ($columns->where('field', $orderBy)->first()['options']['translatable'] ?? false) {
                        return $item->getTranslation($orderBy, $locale);
                    } else {
                        return $item;
                    }
                });
            }

            // Pagination
            $query = $query->slice(($page - 1) * $perPage)->take($perPage);

            // Add read/edit/delete links
            $query->transform(function ($item) {
                // Todo: what if keyName() is translatable?
                $item->computed_actions = [
                    'read'   => route('voyager.'.$this->bread->getTranslation('slug').'.show', $item->getKey()),
                    'edit'   => route('voyager.'.$this->bread->getTranslation('slug').'.edit', $item->getKey()),
                    'delete' => route('voyager.'.$this->bread->getTranslation('slug').'.destroy', $item->getKey()),
                ];

                return $item;
            });

            $rows = $query->values()->toArray();
        }

        return response()->json([
            'records' => $records,
            'rows'    => $rows,
        ]);
    }
}
