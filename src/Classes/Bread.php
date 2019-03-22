<?php

namespace Bread\Classes;

use Bread\BreadFacade;
use Bread\Traits\Translatable;
use Illuminate\Support\Facades\File;

class Bread
{
    use Translatable;

    public $table;
    public $slug;
    public $name_singular;
    public $name_plural;
    public $model_name;
    public $layouts;

    public function __construct($path)
    {
        $content = File::get($path);
        $json = @json_decode($content);
        if (json_last_error() !== JSON_ERROR_NONE) {
            BreadFacade::debug('BREAD file "'.$path.'" is not valid JSON: '.json_last_error_msg());

            return;
        }

        $this->table = pathinfo($path)['filename'];
        $this->slug = $json->slug ?? null;
        $this->name_singular = $json->name_singular ?? null;
        $this->name_plural = $json->name_plural ?? null;
        $this->model_name = $json->model_name ?? '';
        $this->layouts = collect();

        foreach ($json->layouts ?? [] as $layout) {
            $layout = new Layout($layout);
            if ($layout->isValid()) {
                $this->layouts->push($layout);
            }
        }
    }

    public function getModel()
    {
        if ($this->model_name) {
            return app($this->model_name);
        }
    }

    public function getLayout($action)
    {
        $user_roles = app('VoyagerAuth')->user()->roles_all()->pluck('id')->toArray();
        $type = $action == 'browse' ? 'list' : 'view';

        return $this->layouts->filter(function ($layout) use ($action, $type, $user_roles) {
            return $layout->type == $type && count(array_intersect($layout->{$action.'_roles'}, $user_roles)) > 0;
        })->first();
    }

    public function isValid()
    {
        if (!$this->table) {
            return false;
        } elseif (!$this->slug) {
            BreadFacade::debug('BREAD "'.$this->table.'" is not valid because the slug is missing');

            return false;
        } elseif (!$this->name_singular) {
            BreadFacade::debug('BREAD "'.$this->table.'" is not valid because the singular name is missing');

            return false;
        } elseif (!$this->name_plural) {
            BreadFacade::debug('BREAD "'.$this->table.'" is not valid because the plural name is missing');

            return false;
        }

        return true;
    }
}
