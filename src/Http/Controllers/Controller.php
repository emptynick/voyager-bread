<?php

namespace Bread\Http\Controllers;

use Bread\BreadFacade;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;

    public function getSlug(Request $request)
    {
        if ($request->route()) {
            return explode('.', $request->route()->getName())[1];
        }
        return null;
    }

    public function getLayout($action)
    {
        //Collect all role-ids for the user
        $roles = collect(\Auth::user()->roles->pluck('id'));
        $roles[] = \Auth::user()->role->id;
        $roles = $roles->unique();
        $action_type = $action.($action == 'browse' ? '_list' : '_view');
        $type = ($action == 'browse' ? 'list' : 'view');
        return $this->bread->layouts->whereIn($action.'_roles', $roles)->first()
            ?: $this->bread->layouts->where('name', $this->bread->{$action_type})->first();
    }

    public function prepareLayout($layout, $model)
    {
        $layout->elements = $layout->elements->map(function ($element) use ($model) {
            $element->options->put('isTranslatable', (
                $model->isTranslatable && $model->isFieldTranslatable($element->field)
            ));
            return $element;
        });
        return $layout;
    }

    public function getRelationships($bread)
    {
        if (isset($bread->model) && class_exists($bread->model)) {
            $model = app($bread->model);
            if ($model->relationships) {
                $relationships =  collect($model->relationships)->map(function ($name) use ($model) {
                    $relationship = $model->{$name}();
                    $related_bread = BreadFacade::getBread($relationship->getRelated()->getTable());
                    $info = collect([]);
                    $info->put('name', $name);
                    $info->put('type', class_basename($relationship));
                    $info->put('type_slug', str_slug(class_basename($relationship)));
                    $info->put('has_bread', boolval($related_bread));
                    if (boolval($related_bread)) {
                        $info->put('lists', $related_bread->layouts->where('type', 'list'));
                        $info->put('views', $related_bread->layouts->where('type', 'view'));
                    }
                    return $info;
                });
                return $relationships;
            }
        }
        return collect([]);
    }

    public function getAccessors($bread)
    {
        if (isset($bread->model) && class_exists($bread->model)) {
            $model = app($bread->model);
            if ($model->accessors) {
                return collect($model->accessors);
            }
        }
        return collect([]);
    }

    public function getRelationshipJoin($data, $relationship)
    {
        //Todo $data->join('name', 'parent', '=', 'foreign');
        return $data;
    }

    public function getValidation($layout)
    {
        $rules = [];
        $messages = [];

        foreach ($layout->elements as $element) {
            $rules[$element->field] = [];
            foreach ($element->validation_rules as $rule) {
                $rule_only = substr($rule->rule, 0, (strpos($rule->rule, ':') ?: strlen($rule->rule)));
                $rules[$element->field][] = $rule->rule;
                $messages[$element->field.'.'.$rule_only] = $rule->msg;
            }
        }

        return [
            'rules'  =>   $rules,
            'messages' => $messages
        ];
    }

    public function getProcessedInput(Request $request, $layout)
    {
        $data = collect();

        foreach ($layout->elements as $element) {
            $returned = $element->store($request->{$element->field});
            if (!is_null($returned)) {
                if (gettype($returned) == 'array') {
                    foreach ($returned as $key => $value) {
                        $data->put($key, $value);
                    }
                } else {
                    $data->put($element->field, $returned);
                }
            }
        }

        return $data;
    }
}
