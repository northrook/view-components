<?php

declare(strict_types=1);

namespace Core\View;

use Core\AssetManager;
use Core\View\Component\Arguments;
use Core\View\ComponentFactory\ViewComponent;
use Core\View\ImageComponent\{Blurhash, Image, Sources};
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
     * @param string  $type
     *
     * @return $this
     */
    public function __invoke(
        string  $src,
        string  $alt = '',
        ?string $caption = null,
        ?string $credit = null,
        string  $type = 'image',
    ) : self {
        $image = $this->assetManager->getImage( $src, $this->attributes->get( 'asset-id' ) );

        $this->attributes->class->add( ['image', $this->type], true );

        $this->blurhash = new Blurhash( $image );
        $this->sources  = new Sources( $image );
        $this->image    = new Image( $image, $this->alt );
        $this->aspect   = $image->aspect;
        $this->caption  = $caption;
        $this->credit   = $credit;

        $this->attributes->set( 'asset-id', $image->assetID );

        return $this;
    }

    public static function prepareArguments( Arguments $arguments ) : void
    {
        $arguments
            ->add( 'src', $arguments->attributes->pull( 'src' ) ?? self::FALLBACK )
            ->add( 'alt', $arguments->attributes->pull( 'alt' ) ?? '' )
            ->add( 'caption', $arguments->attributes->pull( 'caption' ) ?? null )
            ->add( 'credit', $arguments->attributes->pull( 'credit' ) ?? null );

        dump( $arguments );
    }
}
