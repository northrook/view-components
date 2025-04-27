<?php

declare(strict_types=1);

namespace Core\View;

use Core\View\ComponentFactory\ViewComponent;
use InvalidArgumentException;
use RuntimeException;

#[ViewComponent( 'svg:{name}:{mod}' )]
final class SvgComponent extends Component
{
    public readonly Element $svg;

    public function __construct( private readonly IconProviderService $iconProvider ) {}

    /**
     * @param string          $name
     * @param null|int|string $size
     * @param mixed           ...$attributes
     *
     * @return $this
     */
    public function __invoke(
        ?string         $name = null,
        null|int|string $size = null,
        mixed        ...$attributes,
    ) : self {
        if ( ! $name ) {
            $message = $this::class.': No icon name provided.';
            $this->logger?->error( $message );
            throw new InvalidArgumentException( $message );
        }
        if ( $size !== null ) {
            $size                 = \is_int( $size ) ? "{$size}px" : $size;
            $attributes['width']  = $size;
            $attributes['height'] = $size;
        }
        $this->svg = $this->iconProvider->getSvg( $name, ...$attributes )
                     ?? throw new RuntimeException(
                         $this::class."No icon found for '{$name}'.",
                     );
        return $this;
    }

    public function getString() : string
    {
        $this->svg->attributes->merge( $this->attributes->array );

        return $this->svg->render();
    }
}
