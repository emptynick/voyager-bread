<?php

namespace Bread;

use Bread\Classes\Bread as BreadClass;

class Bread
{
    protected $formfields = [];
    protected $breads = [];

    public function addFormfield($formfield)
    {
        if (gettype($formfield) == 'string') {
            $formfield = app($formfield);
        }
        $this->formfields[$formfield->getCodename()] = $formfield;

        return $this;
    }

    public function formfield($type)
    {
        return $this->formfields[$type] ?? null;
    }

    public function formfields()
    {
        return collect($this->formfields);
    }

    public function getBread($slug)
    {

    }

    public function getBreadByTable($table)
    {

    }

    public function getBreads()
    {

    }

    public function hasBread($slug)
    {

    }

    public function hasBreadByTable($table)
    {

    }

    public function saveBread($table, $content)
    {
        
    }

    public function deleteBread($table)
    {

    }
}
