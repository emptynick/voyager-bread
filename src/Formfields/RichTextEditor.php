<?php

namespace Bread\Formfields;

class RichTextEditor extends AbstractFormfield
{
    public $element_type = 'formfield';
    protected $codename = 'richtexteditor';
    protected $name = 'Rich Text Editor';
    public $options = [
        'content' => '',
        'placeholder' => 'Test'
    ];
}
