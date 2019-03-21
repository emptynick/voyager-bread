<?php

namespace Bread\Formfields\Relationships;

use Bread\Formfields\BaseFormfield;

class BelongsToMany extends BaseFormfield
{
    public $type = 'BelongsToMany';
    public $lists = false;
}
