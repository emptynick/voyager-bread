<?php

namespace Bread\Formfields;

abstract class AbstractFormfield
{
    protected $name;
    protected $codename;
    public $element_type = 'formfield'; /* formfield or layout_element */
    public $field;
    public $options = [];

    /**
     * Converts the input to be saved in the database.
     *
     * @param input   The plain object from a Request
     * @param options The options as defined in the Layoutbuilder
     */
    public function parseContent($input, $options)
    {
        return $input;
    }

    public function getCodeName()
    {
        return $this->codename;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setData($data)
    {
        foreach ($data as $key => $value) {
            if ($key == 'options') {
                $this->options = array_merge($this->options, (array) $value);
            } else {
                $this->{$key} = $value;
            }
        }
    }

    public function getOptions()
    {
        return json_encode(collect($this->options)->merge(['title' => '']));
    }

    public function getComponent($render = false)
    {
        if ($render) {
            return view('bread::vue.formfields.'.$this->codename);
        } else {
            return 'bread::vue.formfields.'.$this->codename;
        }
    }

    public function validate()
    {
        return
            isset($this->field);
    }
}
