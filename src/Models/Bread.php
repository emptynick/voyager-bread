<?php

namespace Bread\Models;

use Bread\BreadFacade;
use Illuminate\Database\Eloquent\Model;

class Bread extends Model
{
    protected $guarded = ['id'];
    protected $table = 'bread';

    public function views()
    {
        return $this->hasMany(BreadFacade::modelClass('BreadView'))->whereViewType('view');
    }

    public function view($id, $action = null)
    {
        $default_view = $this->views->find($id);

        if (!isset($action)) {
            return $default_view;
        } else {
            $view = $this->views()->whereHas('roles', function ($q) use ($action) {
                $q->where('id', auth()->user()->role->id)->where('action', $action);
            })->first();

            return ((isset($view)) ? $view : $default_view);
        }
    }

    public function lists()
    {
        return $this->hasMany(BreadFacade::modelClass('BreadView'))->whereViewType('list');
    }

    public function list($id, $action = null)
    {
        $default_view = $this->lists->find($id);

        if (!isset($action)) {
            return $default_view;
        } else {
            $view = $this->lists()->whereHas('roles', function ($q) use ($action) {
                $q->where('id', auth()->user()->role->id)->where('action', $action);
            })->first();

            return ((isset($view)) ? $view : $default_view);
        }
    }

    public function getModelAttribute()
    {
        if (isset($this->model_name)) {
            return app($this->model_name);
        } else {
            try {
                return app('\App\\'.studly_case($this->table_name));
            } catch (\Exception $err) {
                throw new \Exception('Please set a Model in Bread-Edit, or create "App\\'.studly_case($this->table_name).'"!');
            }
        }
    }
}
