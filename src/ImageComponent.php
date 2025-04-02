<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\Asset\ImageAsset;
use Core\AssetManager;
use Core\View\Attribute\ViewComponent;
use Core\View\Template\Component;

#[ViewComponent( ['img', 'img:{type}'], true, 60 )]
final class ImageComponent extends Component
{
    protected string $source;

    protected ?string $type = null;

    public readonly ImageAsset $asset;

    public function __construct( private readonly AssetManager $assetManager ) {}

    protected function resolveAsset() : void
    {
        if ( isset( $this->asset ) ) {
            return;
        }

        $imageAsset = $this->assetManager->getAsset( $this->source );

        \assert( $imageAsset instanceof ImageAsset );

        $this->asset = $imageAsset;
    }

    protected function prepareArguments( array &$arguments ) : void
    {
        if ( \is_string( $arguments['attributes']['src'] ?? null ) ) {
            $this->source = $arguments['attributes']['src'];
            unset( $arguments['attributes']['src'] );
        }
    }

    /**
     * @return array{src: string}
     */
    protected function getTemplateParameters() : array
    {
        return ['src' => $this->source];
    }
}
