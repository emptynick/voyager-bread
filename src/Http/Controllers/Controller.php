<?php

namespace Bread\Http\Controllers;

use Bread\BreadFacade;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use Validator;

class Controller extends BaseController
{
    use AuthorizesRequests;

    protected $bread;

    public function __construct(Request $request)
    {
        if ($request->route()) {
            $this->bread = BreadFacade::getBreadBySlug(explode('.', $request->route()->getName())[1]);
        }
    }

    public function getValidator(Request $request, $formfields)
    {
        $computed_rules = [];
        $computed_messages = [];

        $formfields->each(function ($formfield) use (&$computed_rules, &$computed_messages) {
            $field = $formfield->options->field;

            if (($formfield->options->translatable ?? false)) {
                foreach (BreadFacade::getLocales() as $locale) {
                    $computed_rules[$field.'.'.$locale] = '';
                    collect($formfield->validation)->each(function ($rule) use (
                        $locale,
                        $field,
                        &$computed_rules,
                        &$computed_messages
                    ) {
                        if (($rule->deep ?? false) || $locale == BreadFacade::getLocale()) {
                            $computed_rules[$field.'.'.$locale] .= $rule->rule.'|';
                            $message = str_replace(
                                ':locale',
                                strtoupper($locale),
                                BreadFacade::getTranslation($rule->message)
                            );
                            $key = $field.'.'.$locale.'.'.Str::before($rule->rule, ':');
                            $computed_messages[$key] = $message;
                        }
                    });
                    if ($computed_rules[$field.'.'.$locale]) {
                        $computed_rules[$field.'.'.$locale] = substr(
                            $computed_rules[$field.'.'.$locale],
                            0,
                            -1
                        );
                    }
                }
            } else {
                $computed_rules[$field] = '';
                collect($formfield->validation)->each(function ($rule) use (
                    $field,
                    &$computed_rules,
                    &$computed_messages
                ) {
                    $computed_rules[$field] .= $rule->rule.'|';
                    $message = BreadFacade::getTranslation($rule->message);
                    $computed_messages[$field.'.'.Str::before($rule->rule, ':')] = $message;
                });
            }
        });

        return Validator::make(
            $this->processRequest($request, $formfields),
            array_filter($computed_rules),
            $computed_messages
        );
    }

    public function processRequest(Request $request, $formfields)
    {
        $newRequest = collect();
        $formfields->each(function ($formfield) use ($request, $newRequest) {
            $field = $formfield->options->field;
            if (($formfield->options->translatable ?? false)) {
                $newRequest->put($field, json_decode($request->get($field), true));
            } else {
                $newRequest->put($field, $request->get($field));
            }
        });

        return $newRequest->toArray();
    }

    public function processData(Request $request, $layout, $action, $data)
    {
        $layout->formfields->each(function ($formfield) use ($request, $action, &$data) {
            $field = $formfield->options->field;
            $data->{$field} = $formfield->$action($request->get($field) ?? '');
        });

        return $data;
    }
}
