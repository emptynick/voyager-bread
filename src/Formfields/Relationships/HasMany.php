<?php

namespace Bread\Formfields\Relationships;

class HasMany extends BaseRelationshipFormfield
{
    protected $name = 'HasMany';
    protected $codename = 'hasmany';
    public $options = [
        'title'          => '',
        'help_text'      => '',
        'scope'          => '',
    ];
}
