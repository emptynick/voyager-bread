<?php

namespace Bread\FormFields;

abstract class AbstractHandler implements HandlerInterface
{
    protected $name;
    protected $codename;
    protected $supports = [];
    protected $viewfile = null;
    protected $bread_row = null;
    public $type = 'formfield';

    public function createMockup($options)
    {
        return view($this->getViewFile('mockup'), [
            'options' => $options,
            'mockup'  => true,
        ]);
    }

    public function createInput($content, $options, $name)
    {
        return view($this->getViewFile('input'), [
            'content' => $content,
            'options' => $options,
            'name'    => $name,
            'input'   => true,
        ]);
    }

    public function createOutput($data, $render = true, $attribute = null)
    {
        $view = view($this->getViewFile('output'), [
            'data'      => $data,
            'output'    => true,
            'multiple'  => ($data instanceof \Illuminate\Support\Collection),
            'attribute' => $attribute,
        ]);

        if ($render) {
            return $view->render();
        } else {
            return $view;
        }
    }

    public function createOptions($options)
    {
        return view($this->getViewFile('options'), [
            'options' => $options,
        ]);
    }

    public function createContent($input, $options)
    {
        return $input;
    }

    public function supports($driver)
    {
        if (empty($this->supports)) {
            return true;
        }

        return in_array($driver, $this->supports);
    }

    public function getCodename()
    {
        if (empty($this->codename)) {
            $name = class_basename($this);

            if (ends_with($name, 'Handler')) {
                $name = substr($name, 0, -strlen('Handler'));
            }

            $this->codename = snake_case($name);
        }

        return $this->codename;
    }

    public function getName()
    {
        if (empty($this->name)) {
            $this->name = ucwords(str_replace('_', ' ', $this->getCodename()));
        }

        return $this->name;
    }

    public function getViewFile($type = 'input')
    {
        if ($this->viewfile === null) {
            $view = 'bread::formfields.'.$this->codename;
        } else {
            $view = $this->viewfile;
        }

        if (view()->exists($view.'.'.$type)) {
            $view = $view.'.'.$type;
        }

        return $view;
    }

    public function setBreadRow($bread_row)
    {
        $this->bread_row = $bread_row;
    }

    public function getBreadRow()
    {
        return $this->bread_row;
    }
}
