<?php

namespace Bread\Formfields;

class BaseFormfield
{
    protected $name;
    protected $codename;
    protected $layout;
    public $group = 'formfield';
    public $field;
    public $options = [];
    public $validation_rules = [];
    public $computed;

    public function __construct()
    {
        $this->computed = collect([]);
    }

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

    public function prepare($bread, $model, $content = null)
    {
        if (is_array($this->computed)) {
            $this->computed = collect();
        }
        if (str_contains($this->field, '|')) {
            //Inject isTranslatable for a relationship
            $parts = explode('|', $this->field);
            if (method_exists($model, $parts[0])) {
                $relationship = $model->{$parts[0]}();
                $related = $relationship->getRelated();
                $this->computed->put('isTranslatable',
                    ($related->isTranslatable && $related->isFieldTranslatable($parts[1]))
                );
            }
        } else {
            $this->computed->put('isTranslatable',
                ($model->isTranslatable && $model->isFieldTranslatable($this->field))
            );
        }

        return $this;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }
}
