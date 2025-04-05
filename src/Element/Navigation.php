<?php

namespace Core\View\Element;

use Core\Interface\{Printable, PrintableClass};
use function Support\slug;

class Navigation implements Printable
{
    use PrintableClass;

    /** @var ListItem[] */
    private array $items = [];

    public readonly string $id;

    public function __construct(
        public readonly string  $root,
        public readonly ?string $current = null,
        ?string                 $id = null,
    ) {
        $this->id = slug( $id ?? "{$root}-navigation", '-' );
    }

    public function __toString() : string
    {
        return '';
    }

    final public function add(
        string       $label,
        ?string      $icon = null,
        string|false $link = false,
        bool         $render = null,
    ) : MenuItem {
        $item = new MenuItem( $label, $icon, $link );

        $this->items[] = $item;

        return $item;
    }
}
