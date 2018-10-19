<?php

namespace Bread\Formfields\Relationships;

use Bread\Formfields\BaseFormfield;

class BaseRelationshipFormfield extends BaseFormfield
{
    public $group = 'relationship';

    public function getComponent($action, $render = false)
    {
        $path = 'bread::formfields.relationships.'.$this->codename.'.'.$action;
        if (!\View::exists($path)) {
            $path = 'bread::formfields.relationships.'.$this->codename;
        }
        if ($render) {
            return view($path);
        } else {
            return $path;
        }
    }

    public function store($input)
    {
        //Todo: return false because we dont actually store anything in the DB (directly)
        return false;
    }
}
