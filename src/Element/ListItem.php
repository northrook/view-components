<?php

namespace Core\View\Component\Element;

use Core\View\Component\ViewElement;
use Core\View\Element\Attributes;
use UnitEnum;

class ListItem extends ViewElement
{
    protected ?ViewElement $parent = null;

    /** @var ViewElement */
    protected array $children = [];

    protected ?string $icon;

    /**
     * @param array                                                $children
     * @param null|array|Attributes|bool|float|int|string|UnitEnum ...$attributes
     */
    public function __construct(
        array                                                   $children = [],
        string|Attributes|bool|int|array|float|UnitEnum|null ...$attributes,
    ) {
        parent::__construct( 'li', ...$attributes );
    }

    protected function build() : array
    {
        return [
            $this->tag->getOpeningTag( $this->attributes ?? null ),
            // ...$this->content->getArray(),
            $this->tag->getClosingTag(),
        ];
    }

    public function setIcon( ?string $icon ) : self
    {
        $this->icon = $icon;
        return $this;
    }

    final public function setParent( ?ViewElement $parent ) : self
    {
        $this->parent = $parent;
        return $this;
    }

    final public function getParent() : ?ViewElement
    {
        return $this->parent;
    }
}
