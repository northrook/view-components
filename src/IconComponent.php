<?php

declare(strict_types=1);

namespace Core\View;

use Core\Interface\IconProviderInterface;
use Core\View\Attribute\ViewComponent;
use Core\View\{Template\Component, Template\Runtime\Html};
use InvalidArgumentException;
use Stringable;

#[ViewComponent( 'icon:{icon}' )]
final class IconComponent extends Component
{
    protected string $icon;

    protected string $fallback = '';

    public Stringable $svg;

    public function __construct( private readonly IconProviderInterface $iconProvider ) {}

    protected function getParameters() : object|array
    {
        if ( ! $this->icon ) {
            $this->logger?->error( $this::class.': No icon key provided.' );
            throw new InvalidArgumentException( 'No icon key provided.' );
        }

        $icon = $this->iconProvider->get( $this->icon, $this->fallback );

        \assert( $icon instanceof Icon );

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
