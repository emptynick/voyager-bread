<?php

namespace Bread\Formfields\Relationships;

class BelongsTo extends BaseRelationshipFormfield
{
    protected $name = 'BelongsTo';
    protected $codename = 'belongsto';
    public $options = [
        'relationship'   => '',
        'title'          => '',
        'help_text'      => '',
        'list'           => '',
        'scope'          => '',
        'allow_add'      => true,
        'add_view'       => '',
        'allow_empty'    => false,
    ];
}
