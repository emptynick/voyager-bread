<?php

namespace Bread\FormFields;

interface HandlerInterface
{
    public function createMockup($options);

    public function createOptions($options);

    public function createInput($content, $options, $name);

    public function createOutput($input, $render = true, $attribute = null);

    public function createContent($input, $options);

    public function supports($driver);

    public function getCodename();

    public function getName();
}
