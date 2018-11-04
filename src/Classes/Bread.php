<?php

namespace Bread\Classes;

class Bread
{
    public $table;
    public $display_name_singular;
    public $display_name_plural;
    public $slug;
    public $model_name;
    public $controller_name;
    public $policy_name;
    public $icon;
    public $layouts = [];

    public function __construct($content)
    {
        foreach ($content as $key => $value) {
            if ($key == 'layouts' && $value) {
                $this->layouts = collect();
                foreach ($value as $layout) {
                    $this->layouts->push(new Layout($layout));
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }

    public function getLayout($action)
    {
        $roles = collect(\Auth::user()->roles->pluck('id'));
        $roles[] = \Auth::user()->role->id;
        $roles = $roles->unique()->toArray();

        $layout = $this->layouts->filter(function ($layout) use ($action, $roles) {
            foreach ($roles as $role) {
                if (in_array($role, $layout->{$action.'_roles'})) {
                    return true;
                }
            }
            return false;
        })->first();
        if (!$layout) {
            throw new \Exception('There\'s no layout for this action ('.$action.') and your roles!');
        }

        return $layout;
    }
}
