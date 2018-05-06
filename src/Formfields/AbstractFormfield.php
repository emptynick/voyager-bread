<?php

namespace Bread\Formfields;

abstract class AbstractFormfield
{
    protected $name;
    protected $codename;
    public $layout_type = 'formfield'; /* formfield or layout_element */
    public $field;
    public $options = [];

    public function browse($content, $options, $multiple = false, $linked = false, $link_action = null)
    {

    }

    public function read($content, $options)
    {

    }

    public function edit($content, $options)
    {

    }

    public function add($options)
    {

    }

    public function mockup($render = false)
    {
        if ($render) {
            return view($this->mockup(true));
        } else {
            return 'bread::formfields.'.$this->codename;
        }
    }

    /**
     *
     * Converts the input to be saved in the database
     *
     * @param input   The plain object from a Request
     * @param options The options as defined in the Layoutbuilder
     *
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
                $this->options = array_merge($this->options, (array)$value);
            } else {
                $this->{$key} = $value;
            }
        }
    }

    public function getOptions()
    {
        return json_encode($this->options);
    }

    public function validate()
    {
        return (
            isset($this->field)
        );
    }
}
