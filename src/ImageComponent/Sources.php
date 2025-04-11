<?php

namespace Core\View\ImageComponent;

use Core\Asset\ImageAsset;
use Core\View\Element;
use Core\View\Template\Runtime\Html;

/**
 * @internal
 */
final class Sources extends Html
{
    public function __construct( private readonly ImageAsset $asset )
    {
        parent::__construct();
    }

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
            $this->addValue(
                Element::source(
                    srcset : $image['assetUrl'],
                    media  : "(min-width: {$image['width']}px)",
                ),
            );
        }

        return parent::__toString();
    }
}
