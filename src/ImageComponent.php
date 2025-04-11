<?php

declare(strict_types=1);

namespace Core\View;

use Core\AssetManager;
use Core\View\ComponentFactory\ViewComponent;
use Core\View\ImageComponent\{Blurhash, Image, Sources};
use Core\View\Template\Component;

#[ViewComponent( ['img', 'img:{type}'] )]
final class ImageComponent extends Component
{
    protected const string FALLBACK = '';

    public string $source;

    public string $alt = '';

    public ?string $type = 'image';

    public readonly ?string $assetID;

    public readonly Blurhash $blurhash;

    public readonly Sources $sources;

    public readonly Image $image;

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

    protected function prepareArguments(
        array & $properties,
        array & $attributes,
        array & $actions,
        array & $content,
    ) : void {
        $properties['source']  = $attributes['src']     ?? $this::FALLBACK;
        $properties['alt']     = $attributes['alt']     ?? '';
        $properties['caption'] = $attributes['caption'] ?? null;
        $properties['credit']  = $attributes['credit']  ?? null;

        unset( $attributes['src'], $attributes['alt'], $attributes['caption'], $attributes['credit'] );
    }

    protected function getParameters() : array|object
    {
        $this->assetID = $this->attributes->get( 'asset-id' );

        $this->attributes->class->add( 'image', prepend : true );
        $this->attributes->class->add( $this->type );

        $image = $this->assetManager->getImage( $this->source, $this->assetID );

        $this->blurhash = new Blurhash( $image );
        $this->sources  = new Sources( $image );
        $this->image    = new Image( $image, $this->alt );

        $this->attributes->add( 'asset-id', $image->assetID );

        return parent::getParameters();
    }
}
