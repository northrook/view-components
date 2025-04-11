<?php

namespace Core\View\ImageComponent;

use Core\Asset\ImageAsset;
use Core\View\Element;
use Core\View\Template\Runtime\Html;

/**
 * @internal
 */
final class Image extends Html
{
    public function __construct(
        private readonly ImageAsset $asset,
        protected string            $alt,
    ) {
        parent::__construct();
    }

    /**
     * @param string $alt
     *
     * @return $this
     */
    public function __invoke( string $alt ) : self
    {
        $this->alt = $alt;
        return $this;
    }

    public function __toString() : string
    {
        return Element::img(
            $this->asset->getFallbackSource(),
            $this->alt,
            asset_id : $this->asset->assetID,
            style    : [
                'width'        => '100%',
                'height'       => 'auto',
                'aspect-ratio' => $this->asset->aspect->getFloat(),
            ],
        );
    }
}
