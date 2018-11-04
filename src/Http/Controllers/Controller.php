<?php

namespace Bread\Http\Controllers;

use Bread\BreadFacade;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use TCG\Voyager\Database\Schema\SchemaManager;

class Controller extends BaseController
{
    use AuthorizesRequests;

    public function __construct(Request $request)
    {
        if ($request->route()) {
            $route_name = $request->route()->getName();
            if (!starts_with($route_name, 'voyager.bread.')) {
                $this->slug = explode('.', $route_name)[1];
                $this->action = explode('.', $route_name)[2];
                $this->bread = BreadFacade::getBread($this->slug);
                if (!$this->bread) {
                    throw new \Exception('There is no BREAD for this slug ('.$this->slug.').');
                }
                $this->model = app($this->bread->model_name);
                //Middleware did not run (yet)
                $this->middleware(function ($request, $next) {
                    if ($this->action == 'index' || $this->action == 'data') {
                        $this->layout = $this->bread->getLayout('browse')->prepare($this->bread, $this->model);
                    } else if ($this->action == 'show') {
                        $this->layout = $this->bread->getLayout('read')->prepare($this->bread, $this->model);
                    } else if ($this->action == 'edit' || $this->action == 'update') {
                        $this->layout = $this->bread->getLayout('edit')->prepare($this->bread, $this->model);
                    } else if ($this->action == 'create' || $this->action == 'store') {
                        $this->layout = $this->bread->getLayout('add')->prepare($this->bread, $this->model);
                    }

                    return $next($request);
                });
            }
        }
    }

    public function getAccessors($model)
    {
        $accessors = collect();
        if ($model && $model->accessors) {
            $accessors = collect($model->accessors);
        }

        return $accessors;
    }

    public function getRelationships($model, $resolve = false)
    {
        $relationships = collect();
        if ($model && $model->relationships) {
            $relationships = collect($model->relationships)
            ->transform(function ($relationship) use ($model, $resolve) {
                if ($resolve) {
                    $rl = $model->{$relationship}();
                    $bread = BreadFacade::getBreadByTable($rl->getRelated()->getTable());
                    $relationship = [
                        'name'      => $relationship,
                        'type'      => class_basename($rl),
                        'fields'    => SchemaManager::describeTable($rl->getRelated()->getTable())->keys(),
                        'lists'     => $bread ? $bread->layouts->where('type', 'list') : [],
                        'views'     => $bread ? $bread->layouts->where('type', 'view') : [],
                    ];
                }

                return $relationship;
            });
        }

        return $relationships;
    }

    public function prepareContent($content)
    {
        $result = [];
        foreach ($content->getAttributes() as $key => $value) {
            if ($this->layout->elements->where('field', $key)->first()->computed['isTranslatable'] ?? false) {
                $data = json_decode($value);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $result[$key] = $data;
                } else {
                    $result[$key] = $value;
                }
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function processInput($request)
    {
        $data = collect();
        foreach ($this->layout->elements as $element) {
            if ($element->computed['isTranslatable']) {
                $returned = $element->store(json_encode($request->{$element->field}) ?? false);
            } else {
                $returned = $element->store($request->{$element->field} ?? false);
            }

            if ($returned !== false) {
                if (gettype($returned) == 'array') {
                    foreach ($returned as $key => $value) {
                        $data->put($key, $value);
                    }
                } else {
                    //Attribute is translatable and casted
                    if ($element->computed['isTranslatable'] && array_key_exists($element->field, $this->model->getCasts())) {
                        $data->put($element->field, json_decode($returned));
                    } else {
                        $data->put($element->field, $returned);
                    }
                }
            }
        }
        return $data;
    }

    public function getValidationRules()
    {
        $rules = [];
        $messages = [];
        $separator = config('bread.fully_validate_translatable_fields', false) ? '*' : app()->getLocale();
        foreach ($this->layout->elements as $element) {
            foreach ($element->validation as $rule) {
                $rule_only = substr($rule->rule, 0, (strpos($rule->rule, ':') ?: strlen($rule->rule)));
                if ($element->computed['isTranslatable']) {
                    //Todo: this can be replaced with an asterisk for ALL locales
                    $rules[$element->field.'.'.$separator][] = $rule->rule;
                    $messages[$element->field.'.'.$separator.'.'.$rule_only] = get_translated_value($rule->msg);
                } else {
                    $rules[$element->field][] = $rule->rule;
                    $messages[$element->field.'.'.$rule_only] = get_translated_value($rule->msg);
                }

            }
        }
        return [
            'rules'    => $rules,
            'messages' => $messages,
        ];
    }
}
