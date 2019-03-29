<?php

namespace Bread\Classes;

use Bread\BreadFacade;
use Bread\Traits\Translatable;

class Layout implements \JsonSerializable
{
    use Translatable;

    public $name;
    public $type;
    public $order_by;
    public $browse_roles;
    public $read_roles;
    public $edit_roles;
    public $add_roles;
    public $formfields;

    public function __construct($layout)
    {
        $this->name = $layout->name;
        $this->type = $layout->type ?? 'view';
        $this->order_by = $layout->order_by ?? '';
        $this->browse_roles = $layout->browse_roles ?? [];
        $this->read_roles = $layout->read_roles ?? [];
        $this->edit_roles = $layout->edit_roles ?? [];
        $this->add_roles = $layout->add_roles ?? [];
        $this->formfields = collect();

        foreach ($layout->formfields ?? [] as $data) {
            $formfield = BreadFacade::formfield($data->type);
            if (!$formfield) {
                BreadFacade::debug('Formfield '.ucfirst($data->type).' does not exist!');
                continue;
            }
            $formfield = clone $formfield;
            $formfield->setOptions($data->options ?? new \stdClass());
            $formfield->setValidationRules($data->validation ?? []);
            $this->formfields->push($formfield);
        }
    }

    public function getColumnDefinitions()
    {
        $columns = [];
        $this->formfields->each(function ($formfield) use (&$columns) {
            $columns[] = (object) [
                'label'         => $this->getTranslationFromObject($formfield->options->title),
                'field'         => $formfield->options->field,
                'type'          => $formfield->getType(),
                'sortable'      => $formfield->options->orderable,
                'searchable'    => $formfield->options->searchable,
                'search_text'   => __('bread::bread.filter_by_column', [
                                        'column' => $this->getTranslationFromObject($formfield->options->title),
                                    ]),
                'width'         => (($formfield->options->width ?? 25) * 0.80).'%',
                'options'       => $formfield->options,
                'validation'    => $formfield->validation,
            ];
        });

        return $columns;
    }

    public function isValid()
    {
        return isset($this->name);
    }

    public function jsonSerialize()
    {
        if ($this->type == 'view') {
            unset($this->order_by);
            unset($this->browse_roles);
        } else {
            unset($this->read_roles);
            unset($this->edit_roles);
            unset($this->add_roles);
        }

        return $this;
    }
}
