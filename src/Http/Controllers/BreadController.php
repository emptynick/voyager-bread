<?php

namespace Bread\Http\Controllers;

use Bread\BreadFacade;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Facades\Voyager;

class BreadController extends Controller
{
    public function __construct(Request $request)
    {
        $this->bread = BreadFacade::getBread($this->getSlug($request));
        if ($this->bread) {
            $this->model = app($this->bread->model);
        }
    }

    public function index()
    {
        $this->authorize('browse', app($this->bread->model));

        $layout = $this->getLayout('browse')->translate();
        $view = 'bread::bread.browse';
        if (view()->exists('bread::'.$this->bread->slug.'.browse')) {
            $view = 'bread::'.$this->bread->slug.'.browse';
        }

        return Voyager::view($view, [
            'bread'  => $this->bread,
            'model'  => $this->model,
            'layout' => $layout,
        ]);
    }

    public function show($id)
    {
        //Todo: check if id is accessible by the user
        //Get browse-list, check if scopes are applied, if yes, check if $model->scope->where(id, $id)->findOrFail()
        $content = $this->model->findOrFail($id);
        $this->authorize('read', $content);
        $layout = $this->getLayout('read')->translate();
        $view = 'bread::bread.read';
        if (view()->exists('bread::'.$this->bread->slug.'.read')) {
            $view = 'bread::'.$this->bread->slug.'.read';
        }

        return Voyager::view($view, [
            'bread'   => $this->bread,
            'model'   => $this->model,
            'layout'  => $layout,
            'content' => $content,
        ]);
    }

    public function edit($id)
    {
        //Todo: check if id is accessible by the user
        //Get browse-list, check if scopes are applied, if yes, check if $model->scope->where(id, $id)->findOrFail()
        $content = $this->model->findOrFail($id);
        $this->authorize('edit', $content);
        $layout = $this->prepareLayout($this->getLayout('edit')->translate(), $this->model);
        $view = 'bread::bread.edit-add';
        if (view()->exists('bread::'.$this->bread->slug.'.edit-add')) {
            $view = 'bread::'.$this->bread->slug.'.edit-add';
        }

        return Voyager::view($view, [
            'bread'   => $this->bread,
            'model'   => $this->model,
            'layout'  => $layout,
            'content' => $content,
        ]);
    }

    public function update(Request $request, $id)
    {
        $content = $this->model->findOrFail($id);
        $this->authorize('edit', $content);
        $layout = $this->getLayout('edit');

        $validation = $this->getValidation($layout);
        $validator = Validator::make($request->all(), $validation['rules'], $validation['messages'])->validate();

        $data = $this->getProcessedInput($request, $layout)->toArray();
        foreach ($data as $key => $value) {
            if ($key) {
                $content->{$key} = $value;
            }
        }
        $content->save();

        if ($request->has('submit_action')) {
            if ($request->submit_action == 'edit') {
                return redirect()
                        ->route("voyager.{$this->bread->slug}.edit", $content->getKey())
                        ->with([
                            'message'    => __('voyager::generic.successfully_updated')." {$this->bread->display_name_singular}",
                            'alert-type' => 'success',
                        ]);
            } elseif ($request->submit_action == 'add') {
                return redirect()
                        ->route("voyager.{$this->bread->slug}.create")
                        ->with([
                            'message'    => __('voyager::generic.successfully_updated')." {$this->bread->display_name_singular}",
                            'alert-type' => 'success',
                        ]);
            }
        }

        return redirect()
                ->route("voyager.{$this->bread->slug}.index")
                ->with([
                    'message'    => __('voyager::generic.successfully_updated')." {$this->bread->display_name_singular}",
                    'alert-type' => 'success',
                ]);
    }

    public function create()
    {
        $this->authorize('add', $this->model);
        $layout = $this->prepareLayout($this->getLayout('add')->translate(), $this->model);
        $view = 'bread::bread.edit-add';
        if (view()->exists('bread::'.$this->bread->slug.'.edit-add')) {
            $view = 'bread::'.$this->bread->slug.'.edit-add';
        }

        $content = new $this->bread->model();

        //Prefill content with {} for translatable attributes
        if ($this->model->translatable && is_array($this->model->translatable)) {
            foreach ($this->model->translatable as $field) {
                $content->{$field} = '{}';
            }
        }

        return Voyager::view($view, [
            'bread'   => $this->bread,
            'model'   => $this->model,
            'layout'  => $layout,
            'content' => $content,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('add', $this->model);
        $layout = $this->getLayout('add');

        $validation = $this->getValidation($layout);
        $validator = Validator::make($request->all(), $validation['rules'], $validation['messages'])->validate();

        $data = $this->getProcessedInput($request, $layout)->toArray();

        $content = new $this->model();
        foreach ($data as $key => $field) {
            if ($key) {
                $content->{$key} = $field;
            }
        }
        $content->save();

        if ($request->has('submit_action')) {
            if ($request->submit_action == 'edit') {
                return redirect()
                        ->route("voyager.{$this->bread->slug}.edit", $content->getKey())
                        ->with([
                            'message'    => __('voyager::generic.successfully_updated')." {$this->bread->display_name_singular}",
                            'alert-type' => 'success',
                        ]);
            } elseif ($request->submit_action == 'add') {
                return redirect()
                        ->route("voyager.{$this->bread->slug}.create")
                        ->with([
                            'message'    => __('voyager::generic.successfully_updated')." {$this->bread->display_name_singular}",
                            'alert-type' => 'success',
                        ]);
            }
        }

        return redirect()
                ->route("voyager.{$this->bread->slug}.index")
                ->with([
                        'message'    => __('voyager::generic.successfully_added_new')." {$this->bread->display_name_singular}",
                        'alert-type' => 'success',
                    ]);
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('delete', $this->model);

        $ids = [];
        if (empty($id)) {
            $ids = $request->ids;
        } else {
            $ids[] = $id;
        }
        foreach ($ids as $id) {
            $data = $this->model->findOrFail($id);
            //Clean-up everything related
        }
        $data->destroy($ids);
    }

    public function data(Request $request)
    {
        $layout = $this->getLayout('browse');
        if ($request->has('list')) {
            $layout = $this->bread->layouts->where('type', 'list')->where('name', $request->list)->first();
        }

        extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending']));
        $fields = SchemaManager::describeTable($this->model->getTable())->keys();
        $relationships = $this->getRelationships($this->bread)->toArray();
        $attributes = $this->getAccessors($this->bread)->toArray();
        $data = $this->model->select('*');
        if ($layout->data && $layout->data == 'scope' && $layout->scope && $layout->scope != '') {
            $data = $data->{$layout->scope}();
        }
        if (isset($query) && $query) {
            $data = $data->where(function ($q) use ($query, $fields, $data, $attributes, $relationships) {
                if (is_string($query)) {
                    //Search all searchable fields
                } else {
                    foreach ($query as $field => $term) {
                        if (is_string($term)) {
                            if ($fields->contains($field)) {
                                $q->where($field, 'LIKE', "%{$term}%");
                            } elseif (in_array($field, $attributes)) {
                            } else {
                                $parts = explode('|', $field, 2);
                                if (in_array($parts[0], $relationships)) {
                                    $q->whereHas($parts[0], function ($query) use ($term, $parts) {
                                        $query->where($parts[1], 'LIKE', "%{$term}%");
                                    });
                                }
                            }
                        } else {
                            $start = Carbon::createFromFormat('Y-m-d', $query['start'])->startOfDay();
                            $end = Carbon::createFromFormat('Y-m-d', $query['end'])->endOfDay();
                            $q->whereBetween($field, [$start, $end]);
                        }
                    }
                }
            });
        }
        $count = $data->count();
        $data->limit($limit)->skip($limit * ($page - 1));
        if (isset($orderBy)) {
            $direction = $ascending == 1 ? 'ASC' : 'DESC';
            if ($fields->contains($orderBy)) {
                $data->orderBy($orderBy, $direction);
            } elseif (in_array($orderBy, $attributes)) {
                //
            } else {
                $parts = explode('|', $orderBy, 2);
                if (in_array($parts[0], $relationships)) {
                    /*$relationship = $this->model->{$parts[0]}();
                    $data = $this->getRelationshipJoin($data, $relationship);
                    $data->orderBy($parts[0].'.'.$parts[1], $direction);*/
                }
            }
        }

        $results = $data->get();
        $final = [];
        $fields = $fields->toArray();
        $elements = $layout->elements->pluck('field');

        foreach ($results as $key => $result) {
            foreach ($elements as $name) {
                //Test what $name is
                if (in_array($name, $fields) || in_array($name, $attributes)) {
                    //Its a normal field or an accessor
                    $data = $result->{$name};
                } else {
                    //It should be a relationship-attribute
                    $parts = explode('|', $name, 2);
                    if (in_array($parts[0], $relationships)) {
                        //It IS a relationship-attribute
                        $relationship = $result->{$parts[0]};
                        if ($relationship instanceof Collection) {
                            foreach ($relationship as $i => $related) {
                                if ($i < 3) {
                                    $data .= $related->{$parts[1]};
                                    if ($i <= 1) {
                                        $data .= ', ';
                                    }
                                }
                            }
                            if (count($relationship) > 3) {
                                $data .= ' and '.(count($relationship) - 3).' more';
                            }
                            if (count($relationship) == 0) {
                                $data = __('voyager::generic.none');
                            }
                        } elseif (!isset($relationship)) {
                            $data = '-';
                        } else {
                            $data = $relationship->{$parts[1]};
                        }
                        //Todo: add link to results
                    }
                }

                if (is_array($data)) {
                    //Todo: Consider displaying arrays?
                    $final[$key][$name] = 'Array';
                } else {
                    $final[$key][$name] = $layout->elements->where('field', $name)->first()->browse($data);
                }
                $data = '';
            }

            //Add URLs
            $final[$key]['bread_read'] = route('voyager.'.$this->bread->slug.'.show', $result[$this->model->getKeyName()]);
            $final[$key]['bread_edit'] = route('voyager.'.$this->bread->slug.'.edit', $result[$this->model->getKeyName()]);
            $final[$key]['bread_delete'] = route('voyager.'.$this->bread->slug.'.destroy', $result[$this->model->getKeyName()]);
            $final[$key]['bread_key'] = $result->getKey();
        }

        return [
            'data'  => collect($final)->values(),
            'count' => $count,
        ];
    }
}
