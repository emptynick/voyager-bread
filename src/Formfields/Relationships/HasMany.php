<?php

namespace Bread\Formfields\Relationships;

class HasMany extends BaseRelationshipFormfield
{
    protected $name = 'HasMany';
    protected $codename = 'hasmany';
    public $options = [
        'title'          => '',
        'help_text'      => '',
        'relationship'   => '',
    ];

    public function store($input, $model = null)
    {
        $relationship = $model->{$this->options['relationship']}();

        return false; //Exclude from query
    }
}
