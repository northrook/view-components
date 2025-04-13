<?php

declare(strict_types=1);

namespace Core\View\ImageComponent;

use Core\Asset\ImageAsset;
use Core\View\Element;
use const Support\AUTO;

/**
 * @internal
 */
final class Blurhash extends Element
{
    /** @var null|int<4,64> [AUTO] */
    protected ?int $resolution = AUTO;

    protected bool $aspectRatio = false;

    public function __construct( private readonly ImageAsset $asset )
    {
        parent::__construct( 'blurhash' );
    }

    /**
     * @param null|int<4,64> $resolution    [AUTO]
     * @param bool           $aspectRatio
     * @param mixed          ...$attributes
     *
     * @return $this
     */
    public function __invoke(
        ?int     $resolution = AUTO,
        bool     $aspectRatio = true,
        mixed ...$attributes,
    ) : self {
        $this->resolution  = $resolution;
        $this->aspectRatio = $aspectRatio;
        $this->attributes->merge( $attributes );
        return $this;
    }

    public function __toString() : string
    {
        $this->attributes->style->add(
            "background-image: url({$this->asset->getBlurHashDataUri( $this->resolution )}); background-size: cover;",
        );
        if ( $this->aspectRatio ) {
            $this->attributes->style->add(
                "aspect-ratio: {$this->asset->aspect->getFloat()};",
            );
        }

        return $this->render();
    }
}
