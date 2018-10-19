<?php

namespace Bread\Formfields;

class Password extends BaseFormfield
{
    protected $name = 'Password';
    protected $codename = 'password';

    public $options = [
        'placeholder'    => '',
        'title'          => '',
        'help_text'      => '',
        'keep_password'  => true,
    ];

    public function store($input)
    {
        if ($this->options->keep_password && (!$input || $input == '')) {
            return false; //Returning false will exclude the field from the update-query
        }

        return bcrypt($input ?: '');
    }
}
