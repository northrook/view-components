<?php

declare(strict_types=1);

namespace Core\View;

use Core\AssetManager;
use Core\Asset\ImageAsset;
use Core\View\ComponentFactory\ViewComponent;
use Core\View\Template\Component;

#[ViewComponent( ['img', 'img:{type}'] )]
final class ImageComponent extends Component
{
    protected const string FALLBACK = '';

    public string $source;

    public ?string $type = 'image';

    public readonly ImageAsset $image;

    public readonly ?string $assetID;

    public function __construct( private readonly AssetManager $assetManager ) {}

    /**
     * @param string  $href
     * @param string  $alt
     * @param ?string $caption
     * @param ?string $credit
     *
     * @return $this
     */
    public function __invoke(
        string  $href,
        string  $alt = '',
        ?string $caption = null,
        ?string $credit = null,
    ) : self {
        dump( \get_defined_vars() );
        return $this;
    }

    protected function getParameters() : array|object
    {
        $this->source  = $this->attributes->get( 'src' ) ?? $this::FALLBACK;
        $this->assetID = $this->attributes->get( 'asset-id' );

        $this->image = $this->assetManager->getImage( $this->source, $this->assetID );
        return parent::getParameters();
    }
}
