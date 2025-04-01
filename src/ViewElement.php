<?php

namespace Core\View\Component;

use Core\Interface\View;
use Core\View\Element\{Attributes, Tag};
use UnitEnum;

abstract class ViewElement extends View
{
    public readonly Tag $tag;

    public readonly Attributes $attributes;

    /**
     * @param string                                                    $tag
     * @param null|array<array-key, ?string>|Attributes|scalar|UnitEnum ...$attributes
     */
    public function __construct(
        string                                                  $tag,
        Attributes|array|null|bool|float|int|string|UnitEnum ...$attributes,
    ) {
        $this->tag        = Tag::from( $tag );
        $this->attributes = new Attributes( ...$attributes );
    }

    /**
     * @return string|string[]
     */
    abstract protected function build() : string|array;

    final protected function render() : string
    {
        return \implode( '', (array) $this->build() );
    }

    public function __toString() : string
    {
        return $this->render();
    }
}
