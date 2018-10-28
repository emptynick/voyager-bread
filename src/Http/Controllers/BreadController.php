<?php

namespace Bread\Http\Controllers;

use Bread\BreadFacade;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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
        $this->authorize('browse', $this->model);

        $layout = $this->getLayout('browse');
        $view = 'bread::bread.browse';
        if (view()->exists('bread::'.$this->bread->slug.'.browse')) {
            $view = 'bread::'.$this->bread->slug.'.browse';
        }

        return Voyager::view($view, [
            'bread'       => $this->bread,
            'model'       => $this->model,
            'layout'      => $layout,
            'soft_delete' => in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->model)),
        ]);
    }

    public function show($id)
    {
        $content = $this->model->findOrFail($id);

        $this->authorize('read', $content);
        $layout = $this->prepareLayout($this->getLayout('read'), $this->model);
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

    public function edit(Request $request, $id)
    {
        $content = $id instanceof Model ? $id : $this->model->findOrFail($id);

        $this->authorize('edit', $content);
        $layout = $this->prepareLayout($this->getLayout('edit'), $this->model);
        //Load relationships
        $layout->elements->where('group', 'relationship')->each(function ($el) use ($content) {
            if (!$content->relationLoaded($el->options['relationship'])) {
                $content->load($el->options['relationship'].':id'); //Todo: replace this
            }
        });

        $view = 'bread::bread.edit-add';
        if (view()->exists('bread::'.$this->bread->slug.'.edit-add')) {
            $view = 'bread::'.$this->bread->slug.'.edit-add';
        }

        return Voyager::view($view, [
            'bread'   => $this->bread,
            'model'   => $this->model,
            'layout'  => $layout,
            'content' => $content,
            'ajax'    => $request->ajax(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $content = $this->model->findOrFail($id);

        $this->authorize('edit', $content);
        $layout = $this->getLayout('edit');

        $data = $this->getProcessedInput($request, $layout)->toArray();
        dd($data);

        $validation = $this->getValidation($layout);
        //We need to extract the default locale and pass it to the validator
        $fields = $request->all();
        foreach ($fields as $key => $value) {
            if ($this->model->isFieldTranslatable($key)) {
                $fields[$key] = get_translated_value_recursive($value);
            }
        }

        $validator = Validator::make($fields, $validation['rules'], $validation['messages']);
        if ($validator->fails()) {
            //Push back original data to validator
            $validator->setData($request->all());

            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $data = $this->getProcessedInput($request, $layout)->toArray();

        foreach ($data as $key => $value) {
            if ($key) {
                $el = $layout->elements->where('field', $key)->first();
                if ($el->getCodename() == 'repeater') {
                    $content->setPlainAttribute($key, $value);
                } else {
                    $content->setAttribute($key, $value);
                }
            }
        }
        $content->save();

        if ($request->has('submit_action')) {
            if ($request->submit_action == 'edit') {
                return redirect()
                        ->route('voyager.'.get_translated_value($this->bread->slug).'.edit', $content->getKey())
                        ->with([
                            'message'    => __('voyager::generic.successfully_updated').' '.get_translated_value($this->bread->display_name_singular),
                            'alert-type' => 'success',
                        ]);
            } elseif ($request->submit_action == 'add') {
                return redirect()
                        ->route('voyager.'.get_translated_value($this->bread->slug).'.create')
                        ->with([
                            'message'    => __('voyager::generic.successfully_updated').' '.get_translated_value($this->bread->display_name_singular),
                            'alert-type' => 'success',
                        ]);
            }
        }

        return redirect()
                ->route('voyager.'.get_translated_value($this->bread->slug).'.index')
                ->with([
                    'message'    => __('voyager::generic.successfully_updated').' '.get_translated_value($this->bread->display_name_singular),
                    'alert-type' => 'success',
                ]);
    }

    public function create(Request $request)
    {
        $this->authorize('add', $this->model);
        $layout = $this->prepareLayout($this->getLayout('add'), $this->model);

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
            'ajax'    => $request->ajax(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('add', $this->model);
        $layout = $this->getLayout('add');

        $validation = $this->getValidation($layout);
        //We need to extract the default locale and pass it to the validator
        $fields = $request->all();
        foreach ($fields as $key => $value) {
            if ($this->model->isFieldTranslatable($key)) {
                $fields[$key] = get_translated_value_recursive($value);
            }
        }

        $validator = Validator::make($fields, $validation['rules'], $validation['messages']);
        if ($validator->fails()) {
            //Push back original data to validator
            $validator->setData($request->all());

            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $data = $this->getProcessedInput($request, $layout)->toArray();

        $content = new $this->model();
        foreach ($data as $key => $field) {
            if ($key) {
                $content->{$key} = $field;
            }
        }
        $content->save();

        if ($request->ajax()) {
            return $content->getKey();
        }

        if ($request->has('submit_action')) {
            if ($request->submit_action == 'edit') {
                return redirect()
                        ->route('voyager.'.get_translated_value($this->bread->slug).'.edit', $content->getKey())
                        ->with([
                            'message'    => __('voyager::generic.successfully_added_new').' '.get_translated_value($this->bread->display_name_singular),
                            'alert-type' => 'success',
                        ]);
            } elseif ($request->submit_action == 'add') {
                return redirect()
                        ->route('voyager.'.get_translated_value($this->bread->slug).'.create')
                        ->with([
                            'message'    => __('voyager::generic.successfully_added_new').' '.get_translated_value($this->bread->display_name_singular),
                            'alert-type' => 'success',
                        ]);
            }
        }

        return redirect()
                ->route('voyager.'.get_translated_value($this->bread->slug).'.index')
                ->with([
                        'message'    => __('voyager::generic.successfully_added_new').' '.get_translated_value($this->bread->display_name_singular),
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
            $this->authorize('delete', $data);
            //Todo: Clean-up everything related
        }
        $data->destroy($ids);
    }

    public function restore(Request $request, $id)
    {
        $data = $this->model->findOrFail($id);
        $this->authorize('delete', $data);
        $data->restore();
    }

    public function data(Request $request)
    {
        //Todo: authorize!!!
        $layout = $this->prepareLayout($this->getLayout('browse'), $this->model);
        if ($request->has('list')) {
            $layout = $this->bread->layouts->where('type', 'list')->where('name', $request->list)->first();
        }

        extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'scope']));
        $fields = SchemaManager::describeTable($this->model->getTable())->keys();
        $relationships = $this->getRelationships($this->bread);
        $accessors = $this->getAccessors($this->bread)->toArray();

        $data = $this->model->select('*');
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->model))) {
            if ($layout->trashed == 'select') {
                //Let the user decide what to show
                if ($request->has('withTrashed')) {
                    if ($request->withTrashed == 'no') {
                        $data = $data->whereNull('deleted_at');
                    } elseif ($request->withTrashed == 'only') {
                        $data = $data->whereNotNull('deleted_at');
                    }
                } else {
                    $data = $data->whereNull('deleted_at');
                }
            } elseif ($layout->trashed == 'only') {
                //Only show trashed
                $data = $data->whereNotNull('deleted_at');
            } else {
                //Hide trashed
                $data = $data->whereNull('deleted_at');
            }
        }
        if ($layout->data && $layout->data == 'scope' && $layout->scope && $layout->scope != '') {
            $data = $data->{$layout->scope}();
        }
        if (isset($query) && $query) {
            $data = $data->where(function ($q) use ($query, $fields, $data, $accessors, $relationships, $layout) {
                if (is_string($query)) {
                    foreach ($layout->elements->where('searchable', true)->pluck('field') as $searchable) {
                        $q->orWhere($searchable, 'LIKE', "%{$query}%");
                    }
                } else {
                    foreach ($query as $field => $term) {
                        if (is_string($term)) {
                            if ($fields->contains($field)) {
                                $q->where($field, 'LIKE', "%{$term}%");
                            } elseif ($accessors->contains($field)) {
                                //Todo: search by accessor
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
            } elseif (in_array($orderBy, $accessors)) {
                //Todo: Order by accessor
            } else {
                $parts = explode('|', $orderBy, 2);
                if ($relationships->contains($parts[0])) {
                    //Todo: Order by relationship
                }
            }
        }

        $results = $data->get();
        if ($request->has('include') && $request->include && $request->include != 'null') {
            //We NEED to include the ID(s) passed.
            if (str_contains($request->include, ',')) {
                foreach (explode($request->include, ',') as $id) {
                    $prepend = $this->model->find($id);
                    if (!$results->contains($prepend)) {
                        $results->prepend($prepend);
                    }
                }
            } else {
                $prepend = $this->model->find($request->include);
                if (!$results->contains($prepend)) {
                    $results->prepend($prepend);
                }
            }
        }
        $final = [];
        $fields = $fields->toArray();
        if ($request->has('list')) {
            //Coming from a relationship-select
            $elements = $layout->elements->where('id', $layout->relationship)->pluck('field');
        } else {
            $elements = $layout->elements->pluck('field');
        }

        foreach ($results as $key => $result) {
            if (!$result) {
                continue;
            }
            foreach ($elements as $name) {
                $data = '';
                //Test what $name is
                if (in_array($name, $fields) || in_array($name, $accessors)) {
                    //Its a normal field or an accessor
                    if ($this->model->isTranslatable && $this->model->isFieldTranslatable($name)) {
                        $data = $result->getPlainValue($name);
                    } else {
                        $data = $result->{$name};
                    }
                } elseif (strpos($name, '|') !== false) {
                    //It should be a relationship-attribute
                    $parts = explode('|', $name, 2);
                    $relationship_details = $relationships->where('name', $parts[0])->first();
                    if ($relationship_details) {
                        //It IS a relationship-attribute
                        $rl = $result->{$relationship_details['name']}();
                        $data = collect($rl->pluck($parts[1]));
                        /*$data = implode(', ', $relationship->take(3)->toArray());
                        if (count($relationship) > 3) {
                            $data .= ' and '.(count($relationship) - 3).' more';
                        }
                        if (count($relationship) == 0) {
                            $data = __('voyager::generic.none');
                        }*/
                    }
                }

                $element = $layout->elements->where('field', $name)->first();

                $final[$key][$name]['data'] = $data;
                $final[$key][$name]['type'] = $element->getCodename();
                $final[$key][$name]['options'] = $element->getOptions();
            }

            //Add static stuff
            $final[$key]['bread_read']   = route('voyager.'.get_translated_value($this->bread->slug).'.show', $result[$this->model->getKeyName()]);
            $final[$key]['bread_edit']   = route('voyager.'.get_translated_value($this->bread->slug).'.edit', $result[$this->model->getKeyName()]);
            $final[$key]['bread_delete'] = route('voyager.'.get_translated_value($this->bread->slug).'.destroy', $result[$this->model->getKeyName()]);
            $final[$key]['bread_key']    = $result->getKey();
            $final[$key]['deleted_at']   = $result['deleted_at'] ?? '';
            $final[$key]['restore']      = route('voyager.'.get_translated_value($this->bread->slug).'.restore', $result[$this->model->getKeyName()]);
        }

        return [
            'data'  => collect($final)->values(),
            'count' => $count,
        ];
    }
}
