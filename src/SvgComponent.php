<?php

declare(strict_types=1);

namespace Core\View;

use Core\Interface\IconProviderInterface;
use Core\View\ComponentFactory\ViewComponent;
use Core\View\Template\Component;
use Core\View\Template\Runtime\Html;
use InvalidArgumentException;

#[ViewComponent( 'svg:{icon}' )]
final class SvgComponent extends Component
{
    protected string $icon;

    protected string $fallback = '';

    public function __construct( private readonly IconProviderInterface $iconProvider ) {}

    /**
     * @param string  $get
     * @param ?string $fallback
     *
     * @return $this
     */
    public function __invoke(
        string  $get,
        ?string $fallback = null,
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

        $icon = $this->iconProvider->get( $this->icon, $this->fallback, ...$this->attributes->array );

        \assert( $icon instanceof Icon );

        if ( ! $icon->isValid ) {
            $this->logger?->error(
                $this::class.': No valid icon provided.',
                ['icon' => $icon, 'get' => $this->icon],
            );
            $icon = new Html( '' );
        }

        return (string) $icon;
    }
}
