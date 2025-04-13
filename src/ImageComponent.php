<?php

declare(strict_types=1);

namespace Core\View;

use Core\AssetManager;
use Core\View\ComponentFactory\ViewComponent;
use Core\View\ImageComponent\{Blurhash, Image, Sources};
use Core\View\Template\Component;
use Support\Image\Aspect;
use Stringable;

#[ViewComponent( ['img', 'img:{type}'] )]
final class ImageComponent extends Component
{
    protected const string FALLBACK = '';

    public string $src;

    public string $alt = '';

    public ?string $type = 'image';

    public readonly Blurhash $blurhash;

    public readonly Sources $sources;

    public readonly Image $image;

    public readonly Aspect $aspect;

    public null|string|Stringable $caption;

    public null|string|Stringable $credit;

    public function __construct( private readonly AssetManager $assetManager ) {}

    /**
     * @param string  $src
     * @param string  $alt
     * @param ?string $caption
     * @param ?string $credit
     *
     * @return $this
     */
    public function __invoke(
        string  $src,
        string  $alt = '',
        ?string $caption = null,
        ?string $credit = null,
    ) : self {
        return $this->create(
            ['src' => $src, 'alt' => $alt, 'caption' => $caption, 'credit' => $credit],
        );
    }

    protected function prepareArguments(
        array & $properties,
        array & $attributes,
        array & $actions,
        array & $content,
    ) : void {
        $properties['src']     = $attributes['src']     ?? $this::FALLBACK;
        $properties['alt']     = $attributes['alt']     ?? '';
        $properties['caption'] = $attributes['caption'] ?? null;
        $properties['credit']  = $attributes['credit']  ?? null;

        unset( $attributes['src'], $attributes['alt'], $attributes['caption'], $attributes['credit'] );
    }

    protected function getParameters() : array|object
    {
        $image = $this->assetManager->getImage( $this->src, $this->attributes->get( 'asset-id' ) );

        $this->attributes->class->add( ['image', $this->type], true );

        $this->blurhash = new Blurhash( $image );
        $this->sources  = new Sources( $image );
        $this->image    = new Image( $image, $this->alt );
        $this->aspect   = $image->aspect;

        $this->attributes->set( 'asset-id', $image->assetID );

        return parent::getParameters();
    }
}
