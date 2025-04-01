<?php

namespace Core\View\Component\Element;

use Core\Interface\View;
use const Support\AUTO;

class MenuItem extends ListItem
{
    protected string $label;

    protected ?string $url;

    /**
     * @param string      $label
     * @param null|string $url
     * @param null|string $icon
     * @param View[]      $items
     * @param null|int    $position
     */
    public function __construct(
        string  $label,
        ?string $url = null,
        ?string $icon = null,
        array   $items = [],
        ?int    $position = AUTO,
    ) {
        $this
            ->setLabel( $label )
            ->setUrl( $url )
            ->setIcon( $icon );

        foreach ( $items as $item ) {
            // $this->
        }
    }

    public function setLabel( string $label ) : self
    {
        $this->label = $label;
        return $this;
    }

    public function setUrl( ?string $url ) : self
    {
        $this->url = $url;
        return $this;
    }
}
