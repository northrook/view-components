<?php

namespace Core\View\ImageComponent;

use Core\Asset\ImageAsset;
use Core\Interface\View;
use Core\View\Element;

/**
 * @internal
 */
final class Image extends View
{
    public function __construct(
        private readonly ImageAsset $asset,
        protected string            $alt,
    ) {}

    /**
     * @param string                                $alt
     * @param null|array<array-key, ?string>|scalar ...$attributes
     *
     * @return $this
     */
    public function __invoke(
        string                              $alt,
        array|bool|string|int|float|null ...$attributes,
    ) : self {
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
