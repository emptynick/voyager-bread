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
        'editable'       => true,
        'allow_add'      => true,
        'add_view'       => '',
        'allow_empty'    => false,
        'as_null'        => false,
    ];

    public function store($input, $model = null)
    {
        $relationship = $model->{$this->options['relationship']}();
        if (!$input || $input == '') {
            if ($this->options['as_null']) {
                $input = null;
            } else {
                $input = '';
            }
        }

        return [$relationship->getForeignKey() => $input];
    }
}
