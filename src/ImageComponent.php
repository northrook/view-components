<?php

declare(strict_types=1);

namespace Core\View;

use Core\AssetManager;
use Core\Asset\ImageAsset;
use Core\View\Attribute\ViewComponent;
use Core\View\Template\Component;

#[ViewComponent( ['img', 'img:{type}'] )]
final class ImageComponent extends Component
{
    protected const string FALLBACK = '';

    public string $source;

    public ?string $type = 'image';

    public readonly ImageAsset $image;

    private ?string $assetID;

    public function __construct( private readonly AssetManager $assetManager ) {}

    protected function prepareArguments( array &$arguments ) : void
    {
        $source  = $arguments['attributes']['src']      ?? $this::FALLBACK;
        $assetID = $arguments['attributes']['asset-id'] ?? null;

        \assert( \is_string( $source ) );
        \assert( \is_string( $assetID ) || \is_null( $assetID ) );

        $this->source  = $source;
        $this->assetID = $assetID;

        unset(
            $arguments['attributes']['src'],
            $arguments['attributes']['asset-id'],
        );
    }

    protected function getParameters() : array|object
    {
        $this->image = $this->assetManager->getImage( $this->source, $this->assetID );
        return parent::getParameters();
    }
}
