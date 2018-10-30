<?php

namespace Bread\Formfields;

class Repeater extends BaseFormfield
{
    protected $name = 'Repeater';
    protected $codename = 'repeater';
    public $group = 'layout';
    public $options = [
        'elements'         => [],
        'label'            => '',
    ];

    public function store($input, $model = null)
    {
        return json_encode(array_values($input ?? []));
    }

    public function prepare($bread, $model, $content = null)
    {
        parent::prepare($bread, $model, $content);

        foreach ($this->options['elements'] as $sub) {
            $sub->computed->put('isTranslatable', $this->options['isTranslatable']);
        }

        return $this;
    }
}
