<?php

namespace Bread\Classes;

use Bread\BreadFacade;

class Layout
{
    public $name;
    public $type;
    public $elements;
    public $browse_roles = [];
    public $read_roles = [];
    public $edit_roles = [];
    public $add_roles = [];

    public function __construct($content)
    {
        foreach ($content as $key => $value) {
            if ($key == 'elements' && $value) {
                $this->elements = collect();
                foreach ($value as $element) {
                    $class = BreadFacade::formfield($element->codename);
                    if ($class) {
                        $formfield = new $class($element);
                        $this->elements->push($formfield);
                    }
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }

    public function prepare($bread, $model)
    {
        $this->elements->transform(function ($element) use ($bread, $model) {
            if ($this->type != 'view') {
                if ($element->field->type == 'relationship') {
                    $name = $element->field->type.'.'.$element->field->relationship.'.'.$element->field->name;
                } else {
                    $name = $element->field->type.'.'.$element->field->name;
                }
                $element->computed['field'] = $name;
            }
            //Translate all options if possible
            foreach ($element->options as $key => $value) {
                $element->computed[$key] = get_translated_value($value);
            }
            $translatable = false;
            if ($model->translatable && $element->field != '' && in_array($element->field, $model->translatable)) {
                $translatable = true;
            }
            $element->computed['isTranslatable'] = $translatable;

            return $element->prepare($bread, $model);
        });

        return $this;
    }
}
