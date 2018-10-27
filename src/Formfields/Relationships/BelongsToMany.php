<?php

namespace Bread\Formfields\Relationships;

class BelongsToMany extends BaseRelationshipFormfield
{
    protected $name = 'BelongsToMany';
    protected $codename = 'belongstomany';
    public $options = [
        'title'          => '',
        'help_text'      => '',
        'relationship'   => '',
    ];
}
