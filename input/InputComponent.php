<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\View\Attribute\ViewComponent;
use Core\View\Element;
use Core\View\Template\AbstractComponent;

#[ViewComponent( 'input:{type}', true, 16 )]
final class InputComponent extends AbstractComponent
{
    protected string $type = 'input';

    public function getView() : Element
    {
        $this->view->attributes->set( 'type', $this->type );
        return $this->view;
    }
}
