<?php

namespace Bread\Formfields;

abstract class BaseFormfield
{
    public $options;
    public $validation;
    public $lists = true;
    public $views = true;

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

    // Returns the value for browsing
    public function browse($value)
    {
        return $value;
    }

    // Returns the value for reading
    public function read($value)
    {
        return $value;
    }

    // Returns the value for editing
    public function edit($value)
    {
        return $value;
    }

    // Returns the value to be stored in the database after editing
    public function update($value)
    {
        return $value;
    }

    // Returns the value for adding
    public function add($value)
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
        return;
    }
}
