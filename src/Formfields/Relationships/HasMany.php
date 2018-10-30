<?php

namespace Bread\Formfields\Relationships;

class HasMany extends BaseRelationshipFormfield
{
    protected $name = 'HasMany';
    protected $codename = 'hasmany';
    public $options = [
        'relationship'   => '',
        'title'          => '',
        'help_text'      => '',
        'list'           => '',
        'scope'          => '',
        'editable'       => true,
        'allow_add'      => true,
        'add_view'       => '',
        'allow_empty'    => false,
        'as_null'        => false,
    ];

    public function store($input, $model = null)
    {
        if (method_exists($model, $this->options['relationship'])) {
            $relationship = $model->{$this->options['relationship']}();
        }

        return false; //Exclude from query
    }
}
