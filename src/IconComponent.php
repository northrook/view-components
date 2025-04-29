<?php

declare( strict_types = 1 );

namespace Core\View;

use Core\View\Component\Arguments;
use Core\View\ComponentFactory\ViewComponent;
use InvalidArgumentException;
use RuntimeException;

#[ViewComponent( [
        'icon:{name}:{mod}',
        'svg:{name}:{mod}',
] )]
final class IconComponent extends Component
{
    public Element $svg;

    protected string $_render = 'svg';

    public function __construct( private readonly IconProviderService $iconProvider ) {}

    public static function prepareArguments( Arguments $arguments ) : void
    {
        $arguments->add( '_render', $arguments->node->name );
    }

    /**
     * @param string   $name
     * @param ?string  $mod
     * @param mixed    ...$attributes
     *
     * @return $this
     */
    public function __invoke(
            ?string $name = null,
            ?string $mod = null,
            mixed   ...$attributes,
    ) : self
    {
        if ( !$name ) {
            $message = $this::class . ': No icon name provided.';
            $this->logger?->error( $message );
            throw new InvalidArgumentException( $message );
        }

        if ( $mod ) {
            $name .= ":{$mod}";
        }

        if ( $this->attributes->merge( ...$attributes )->has( 'size' ) ) {
            $size                   = $this->attributes->pull( 'size' );
            $size                   = ( \is_numeric( $size ) && $size > 0 ) ? "{$size}px" : $size;
            $attributes[ 'width' ]  = $size;
            $attributes[ 'height' ] = $size;
        }
        $this->svg = $this->iconProvider->getSvg( $name, ...$attributes )
                     ?? throw new RuntimeException(
                        $this::class . "No icon found for '{$name}'.",
                );
        return $this;
    }

    protected function getTemplatePath() : false | string
    {
        return $this->_render === 'svg' ? false : $this->name;
    }

    public function getString() : string
    {
        $this->svg->attributes->merge( $this->attributes->array );

        return $this->svg->render();
    }
}
