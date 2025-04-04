<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\AssetManager;
use Core\Asset\ImageAsset;
use Core\View\Attribute\ViewComponent;
use Core\View\Template\Component;

#[ViewComponent( ['img', 'img:{type}'], true, 60 )]
final class ImageComponent extends Component
{
    protected const string FALLBACK = '';

    public string $source;

    public ?string $type = null;

    public readonly ImageAsset $image;

    public function __construct( private readonly AssetManager $assetManager ) {}

    protected function prepareArguments( array &$arguments ) : void
    {
        if ( \is_string( $arguments['attributes']['src'] ?? null ) ) {
            $this->source = $arguments['attributes']['src'];
            unset( $arguments['attributes']['src'] );
        }
        else {
            $this->source = $this::FALLBACK;
        }
    }

    protected function getTemplateParameters() : self
    {
        if ( ! isset( $this->image ) ) {
            $imageAsset = $this->assetManager->getAsset( $this->source );

            \assert( $imageAsset instanceof ImageAsset );

            $this->image = $imageAsset;
        }
        return $this;
    }
}
