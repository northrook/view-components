<?php

declare(strict_types=1);

namespace Core\View;

use Core\View\ComponentFactory\ViewComponent;
use Core\View\Template\Component;

#[ViewComponent( 'input:{type}' )]
final class InputComponent extends Component
{
    protected string $type = 'input';

    public string $input;

    protected function getRadio() : string
    {
        return __METHOD__;
    }

    protected function getInput() : string
    {
        return __METHOD__;
    }

    protected function getParameters() : array|object
    {
        // : Create dynamic $this->templateFilename
        $this->input = match ( $this->type ) {
            'radio' => $this->getRadio(),
            default => $this->getInput(),
        };
        return parent::getParameters();
    }
}
