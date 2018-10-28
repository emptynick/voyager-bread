<?php

namespace Bread\Formfields;

class BaseFormfield
{
    protected $name;
    protected $codename;
    public $group = 'formfield';
    public $field;
    public $options = [];
    public $validation_rules = [];

    public function update($input)
    {
        return $input;
    }

    public function store($input, $model = null)
    {
        return $input;
    }

    public function delete($input)
    {
        return $input;
    }

    public function setData($data)
    {
        foreach ($data as $key => $value) {
            if ($key == 'options') {
                $this->options = collect(json_decode($this->getOptions((array) $value)))->merge((array) $value);
            } else {
                $this->{$key} = $value;
            }
        }
    }

    public function getComponent($action, $render = false)
    {
        $path = 'bread::formfields.'.$this->codename.'.'.$action;
        if (!\View::exists($path)) {
            $path = 'bread::formfields.'.$this->codename;
        }
        if ($render) {
            return view($path);
        } else {
            return $path;
        }
    }

    public function getOptions()
    {
        return json_encode(collect($this->options));
    }

    public function getCodename()
    {
        return $this->codename;
    }

    public function getName()
    {
        return $this->name;
    }
}
