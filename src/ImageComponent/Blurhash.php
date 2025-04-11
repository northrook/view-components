<?php

declare(strict_types=1);

namespace Core\View\ImageComponent;

use Core\Asset\ImageAsset;
use Core\View\Template\Runtime\Html;
use const Support\AUTO;

/**
 * @internal
 */
final class Blurhash extends Html
{
    /** @var null|int<4,128> [AUTO] */
    protected ?int $resolution = AUTO;

    public function __construct( private readonly ImageAsset $asset )
    {
        parent::__construct();
    }

    /**
     * @param null|int<4,128> $resolution [AUTO]
     *
     * @return $this
     */
    public function __invoke( ?int $resolution = AUTO ) : self
    {
        $this->resolution = $resolution;
        return $this;
    }

    public function __toString() : string
    {
        return $this->asset->getBlurHash( $this->resolution );
    }
}
