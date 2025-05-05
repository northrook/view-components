<?php

declare(strict_types=1);

namespace Core\View\ImageComponent;

use Core\Assets\ImageAsset;
use Core\View\Element;
use const Support\AUTO;

/**
 * @internal
 */
final class Image extends Element
{
    /**
     * @param ImageAsset          $asset
     * @param string              $alt
     * @param null|'eager'|'lazy' $load
     */
    public function __construct(
        private readonly ImageAsset $asset,
        string                      $alt,
        ?string                     $load = AUTO,
    ) {
        \assert( $load === AUTO || $load === 'eager' || $load === 'lazy' );
        parent::__construct( 'img', alt : $alt, data_load : $load );
    }

    /**
     * @param mixed ...$attributes
     *
     * @return $this
     */
    public function __invoke( mixed ...$attributes ) : self
    {
        $this->attributes->merge( ...$attributes );
        return $this;
    }

    public function __toString() : string
    {
        $this->attributes
            ->set( 'asset-id', $this->asset->getAssetId() )
            ->set( 'src', $this->asset->getFallbackSource() )
            ->set( 'decoding', 'async' )
            ->style(
                width        : '100%',
                height       : 'auto',
                aspect_ratio : $this->asset->aspect->getFloat(),
            );

        return $this->render();
    }
}
