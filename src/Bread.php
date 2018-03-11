<?php

namespace Bread;

use Bread\FormFields\HandlerInterface;

class Bread
{
    protected $models = [
        'Bread'		   => \Bread\Models\Bread::class,
        'BreadView'	=> \Bread\Models\BreadView::class,
        'BreadRow'	 => \Bread\Models\BreadRow::class,
    ];

    protected $formFields = [];

    public function model($name)
    {
        return app($this->models[studly_case($name)]);
    }

    public function modelClass($name)
    {
        return $this->models[$name];
    }

    public function addFormField($handler)
    {
        if (!$handler instanceof HandlerInterface) {
            $handler = app($handler);
        }
        $this->formFields[$handler->getCodename()] = $handler;

        return $this;
    }

    public function formField($type)
    {
        return $this->formFields[$type];
    }

    public function formFields($type = 'formfield')
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver", 'mysql');

        return collect($this->formFields)->filter(function ($after) use ($driver, $type) {
            return $after->supports($driver) && $after->type == $type;
        });
    }

    public function routes()
    {
        require __DIR__.'/../routes/bread.php';
    }
}
