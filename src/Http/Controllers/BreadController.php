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
            $bread = $this->bread;
            unset($bread->layouts);

            $url = route('voyager.'.$bread->getTranslation('slug').'.store');

            return view('bread::bread.edit-add')->with([
                'bread'  => $bread,
                'layout' => $layout,
                'data'   => null,
                'url'    => $url,
            ]);
        }
    }

    public function store(Request $request, $redirect = true)
    {
        if ($this->bread && $layout = $this->bread->getLayout('add')) {
            $validator = $this->getValidator($request, $layout->formfields)->validate();
            $model = $this->bread->getModel();
            $data = $this->processData($request, $layout, 'update', new $model())->save();
        }

        return redirect()->route('voyager.'.$this->bread->getTranslation('slug').'.index');
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
            $data = $this->bread->getModel()->findOrFail($id);
            $validator = $this->getValidator($request, $layout->formfields)->validate();

            $data = $this->processData($request, $layout, 'update', $data)->save();
        }

        return redirect()->route('voyager.'.$this->bread->getTranslation('slug').'.index');
    }

    // Delete
    public function destroy(Request $request, $id)
    {
    }

    public function getData(Request $request)
    {
        $records = 0;
        $rows = [];

        if ($this->bread) {
            $query = $this->bread->getModel()->select('*');
            $perPage = $request->perPage ?? 10;
            $columns = collect($request->columns ?? []);
            $locale = $request->locale ?? BreadFacade::getLocale();

            $this->loadRelationshipsForQuery($query, $columns);
            $this->searchQuery($query, array_filter($request->filter ?? []), $columns);
            $this->orderQuery($query, $request->orderField, ($request->orderDir ?? 'asc'), $columns);

            $records = $query->count();
            // Pagination
            $query = $query->slice((($request->page ?? 1) - 1) * $perPage)->take($perPage);
            // Add read/edit/delete links
            $this->addLinksToQuery($query);
            $rows = $query->values()->toArray();
        }

        return response()->json([
            'records' => $records,
            'rows'    => $rows,
        ]);
    }

    protected function searchQuery(&$query, $filters, $columns)
    {
        foreach ($filters as $field => $filter) {
            if (Str::contains($field, '.')) {
                // Query relationship
                list($relationship, $field) = explode('.', $field);
                $query = $query->whereHas($relationship, function ($rquery) use ($field, $filter) {
                    $rquery->where($field, 'like', '%'.$filter.'%');
                });
            } else {
                // Translatable field
                if (($columns->where('field', $field)->first()['options']['translatable'] ?? false) &&
                    ($columns->where('field', $field)->first()['options']['search_in_locale'] ?? false)) {
                    $query = $query->whereRaw('lower('.$field.'->"$.'.$locale.'") like lower(?)', ["%{$filter}%"]);
                } else {
                    // Normal field search
                    $query = $query->where($field, 'like', '%'.$filter.'%');
                }
            }
        }
    }

    protected function orderQuery(&$query, $field, $direction, $columns)
    {
        $orderMethod = 'sortBy'.($direction == 'asc' ? '' : 'Desc');
        if ($columns->where('field', $field)->first()['options']['translatable'] ?? false) {
            $query = $query->get()->$orderMethod(function ($item) use ($field) {
                if (Str::contains($field, '.')) {
                    // TODO: sort by translatable relationship
                    list($relationship, $field) = explode('.', $column['field']);
                } else {
                    return $item->{$field};
                }
            });
        } else {
            $query = $query->get()->$orderMethod($field);
        }
    }

    protected function loadRelationshipsForQuery(&$query, $columns)
    {
        foreach ($columns as $column) {
            if (Str::contains($column['field'], '.')) {
                list($relationship, $field) = explode('.', $column['field']);
                $query = $query->with($relationship);
            }
        }
    }

    protected function addLinksToQuery(&$query)
    {
        $query->transform(function ($item) {
            // TODO: what if getKey() is translatable?
            $item->computed_actions = [
                'pk'     => $item->getKey(),
                'read'   => route('voyager.'.$this->bread->getTranslation('slug').'.show', $item->getKey()),
                'edit'   => route('voyager.'.$this->bread->getTranslation('slug').'.edit', $item->getKey()),
                'delete' => route('voyager.'.$this->bread->getTranslation('slug').'.destroy', $item->getKey()),
            ];

            return $item;
        });
    }
}
