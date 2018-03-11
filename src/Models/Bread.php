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

    public function view($id)
    {
        return $this->views->find($id);
    }

    public function lists()
    {
        return $this->hasMany(BreadFacade::modelClass('BreadView'))->whereViewType('list');
    }

    public function list($id)
    {
        return $this->lists->find($id);
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
