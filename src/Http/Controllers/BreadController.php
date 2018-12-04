<?php

namespace Bread\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BreadController extends Controller
{
    public $slug;
    public $bread;
    public $model;

    //Browse
    public function index()
    {
        //$this->authorize('browse', $this->model);

        return view('bread::bread.browse', [
            'bread'     => $this->bread,
            'model'     => $this->model,
            'layout'    => $this->layout,
        ]);
    }

    //Add
    public function create()
    {
        //$this->authorize('add', $this->model);

        return view('bread::bread.edit-add', [
            'bread'     => $this->bread,
            'model'     => $this->model,
            'layout'    => $this->layout,
            'content'   => null,
        ]);
    }

    public function store(Request $request, $redirect = true)
    {
        //$this->authorize('add', $this->model);

        $validation = $this->getValidationRules();
        $validator = Validator::make(
            $request->all(),
            $validation['rules'],
            $validation['messages']
        )->validate();

        $data = $this->processInput($request);

        $content = new $this->model();
        foreach ($data as $key => $field) {
            if ($key) {
                $content->{$key} = $field;
            }
        }
        $content->save();
    }

    public function show($id)
    {
        $content = $id instanceof Model ? $id : $this->model->findOrFail($id);
        //$this->authorize('read', $content);

        return view('bread::bread.read', [
            'layout'    => $this->layout,
            'content'   => $content,
        ]);
    }

    public function edit($id)
    {
        $content = $id instanceof Model ? $id : $this->model->findOrFail($id);
        $key = $content->getKey();
        //$this->authorize('edit', $content);
        $content = $this->prepareContent($content);
        $content['primary_key'] = $key;

        return view('bread::bread.edit-add', [
            'bread'     => $this->bread,
            'model'     => $this->model,
            'layout'    => $this->layout,
            'content'   => $content,
        ]);
    }

    public function update(Request $request, $id)
    {
        $content = $id instanceof Model ? $id : $this->model->findOrFail($id);
        //$this->authorize('edit', $content);

        $validation = $this->getValidationRules();
        $validator = Validator::make(
            $request->all(),
            $validation['rules'],
            $validation['messages']
        )->validate();

        $data = $this->processInput($request);

        foreach ($data as $key => $field) {
            if ($key) {
                $content->{$key} = $field;
            }
        }

        $content->save();
    }

    //Delete
    public function destroy(Request $request, $id)
    {
        //$this->authorize('delete', $this->model);
        $keys = collect($id == 0 ? $request->keys : $id);
        // Todo: Delete
    }

    public function restore($id)
    {
        //$this->authorize('delete', $this->model);
    }

    public function data(Request $request)
    {
        //$this->authorize('browse', $this->model);

        extract($request->only(['query', 'limit', 'ascending', 'page', 'byColumn', 'orderBy']));
        $slug = get_translated_value($this->bread->slug);
        $fields = $this->layout->elements->where('field.type', 'field')->pluck('computed.field');
        $accessors = $this->layout->elements->where('field.type', 'accessors')->pluck('computed.field');
        $relationship = $this->layout->elements->where('field.type', 'relationship')->pluck('computed.field');

        $data = $this->model->select('*');
        //Soft-Deletes

        //Search
        if (count($query) > 0) {
            $data = $data->where(function ($q) use ($query) {
                foreach ($query as $field => $term) {
                    if (is_string($term)) {
                        if ($fields->contains($field)) {
                            $q->where(str_after($field, 'field.'), 'LIKE', "%{$term}%");
                        } elseif ($relationships->contains($field)) {
                            //Todo: Search by relationship
                        }
                    }
                }
            });
        }

        //Order
        if (isset($orderBy)) {
            $direction = $ascending == 1 ? 'ASC' : 'DESC';
            if ($fields->contains($orderBy)) {
                $data->orderBy(str_after($orderBy, 'field.'), $direction);
            } elseif ($relationships->contains($orderBy)) {
                //Todo: Order by relationship
            }
        }

        //Limit/Page
        $count = $data->count();
        $data->limit($limit)->skip($limit * ($page - 1));

        //Compute final data
        $final = $data->get()->map(function ($row) use ($slug) {
            $result = [];
            $result['key'] = $row->getKey();
            $result['delete_url'] = route('voyager.'.$slug.'.destroy', $result['key']);
            $result['restore_url'] = route('voyager.'.$slug.'.restore', $result['key']);
            $result['edit_url'] = route('voyager.'.$slug.'.edit', $result['key']);
            $result['read_url'] = route('voyager.'.$slug.'.show', $result['key']);

            foreach ($this->layout->elements as $el) {
                if ($el->field->type == 'field' || $el->field->type == 'accessor') {
                    $result[$el->computed['field']] = $row->getOriginal($el->field->name);
                } elseif ($el->field->type == 'relationship') {
                    $relationship = $row->{$el->field->relationship};
                    if ($relationship) {
                        if (is_a($relationship, \Illuminate\Support\Collection::class)) {
                            $result[$el->computed['field']] = $relationship->pluck($el->field->name);
                        } else {
                            $result[$el->computed['field']] = $relationship->getOriginal($el->field->name);
                        }
                    }
                }
            }

            return $result;
        });

        return [
            'data'  => $final,
            'count' => $count,
        ];
    }
}
