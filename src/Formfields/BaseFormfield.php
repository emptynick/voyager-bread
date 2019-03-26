<?php

namespace Bread\Formfields;

abstract class BaseFormfield
{
    public $options;
    public $validation;
    public $lists = true; // Can be used in Lists
    public $views = true; // Can be used in views

    public function getType()
    {
        return $this->type;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function setValidationRules($rules)
    {
        $this->validation = $rules;
    }

    // Returns the value to be stored in the database after editing
    public function update($value)
    {
        return $value;
    }

    // Returns the value to be stored in the database after adding
    public function store($value)
    {
        return $value;
    }

    // Callback after deleting a BREAD entry
    public function delete($value)
    {
    }
}
