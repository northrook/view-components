<?php

declare(strict_types=1);

namespace Core\View;

use Core\Interface\IconProviderInterface;
use Core\View\ComponentFactory\ViewComponent;
use Core\View\Template\Component;
use Core\View\Template\Runtime\Html;
use InvalidArgumentException;
use Stringable;

#[ViewComponent( 'icon:{icon}:{size}' )]
final class IconComponent extends Component
{
    protected string $icon;

    protected ?int $size;

    protected string $fallback = '';

    public Stringable $svg;

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

    protected function getParameters() : object|array
    {
        if ( ! $this->icon ) {
            $this->logger?->error( $this::class.': No icon key provided.' );
            throw new InvalidArgumentException( 'No icon key provided.' );
        }

        $icon = $this->iconProvider->get( $this->icon, $this->fallback );

        \assert( $icon instanceof Icon );

        if ( $this->size ) {
            // TODO : Add $icon->attributes() to Icon::class
        }

        if ( ! $icon->isValid ) {
            $this->logger?->error(
                $this::class.': No valid icon provided.',
                ['icon' => $icon, 'get' => $this->icon],
            );
            $icon = new Html( '' );
        }

        $this->svg = $icon;
        return parent::getParameters();
    }
}
