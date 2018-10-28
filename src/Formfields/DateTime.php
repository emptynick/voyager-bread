<?php

namespace Bread\Formfields;

class DateTime extends BaseFormfield
{
    protected $name = 'DateTime';
    protected $codename = 'datetime';

    public $options = [
        'type'           => 'datetime', //date, datetime, time
        'help_text'      => '',
        'title'          => '',
        'min_from'       => '',
    ];

    public function store($input, $model = null)
    {
        if ($this->options['type'] == 'datetime') {
            return \Carbon\Carbon::parse($input)->toDateTimeString();
        } elseif ($this->options['type'] == 'date') {
            return \Carbon\Carbon::parse($input)->toDateString();
        } elseif ($this->options['type'] == 'time') {
            return \Carbon\Carbon::parse($input)->toTimeString();
        }

        return $input;
    }
}
