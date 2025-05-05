<?php

namespace Core\View\ImageComponent;

use Core\Assets\ImageAsset;
use Core\Interface\View;
use Core\View\Element;

/**
 * @internal
 */
final class Sources extends View
{
    /** @var Element[] */
    private array $source = [];

    public function __construct( private readonly ImageAsset $asset ) {}

    /**
     * @return $this
     */
    public function __invoke() : self
    {
        return $this;
    }

    public function __toString() : string
    {
        foreach ( $this->asset->getSrcset() as $image ) {
            $this->source[] = Element::source(
                srcset : $image['assetUrl'],
                media  : "(min-width: {$image['width']}px)",
            );
        }

        return \implode( '', $this->source );
    }
}
