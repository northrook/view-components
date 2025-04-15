<?php

declare(strict_types=1);

namespace Core\View;

use Core\View\ComponentFactory\ViewComponent;
use InvalidArgumentException;

#[ViewComponent( 'svg:{icon}' )]
final class SvgComponent extends Component
{
    protected string $icon;

    public function __construct( private readonly IconProviderService $iconProvider ) {}

    /**
     * @param string $get
     * @param mixed  ...$attributes
     *
     * @return $this
     */
    public function __invoke(
        string   $get,
        mixed ...$attributes,
    ) : self {
        dump( \get_defined_vars() );
        return $this;
    }

    public function getString() : string
    {
        if ( ! $this->icon ) {
            $this->logger?->error( $this::class.': No icon key provided.' );
            throw new InvalidArgumentException( 'No icon key provided.' );
        }

        $icon = $this->iconProvider->getSvg( $this->icon, ...$this->attributes->array );

        \assert( $icon instanceof Element );

        return (string) $icon;
    }
}
