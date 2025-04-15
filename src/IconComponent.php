<?php

declare(strict_types=1);

namespace Core\View;

use Core\View\ComponentFactory\ViewComponent;
use InvalidArgumentException;

#[ViewComponent( 'icon:{icon}:{size}' )]
final class IconComponent extends Component
{
    protected string $icon;

    protected ?int $size;

    public Element $svg;

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

    protected function prepareArguments(
        array &   $properties,
        array &   $attributes,
        array &   $actions,
        ?string & $content,
    ) : void {
        unset( $properties['tag'] );
    }

    protected function getParameters() : object|array
    {
        if ( ! $this->icon ) {
            $this->logger?->error( $this::class.': No icon key provided.' );
            throw new InvalidArgumentException( 'No icon key provided.' );
        }

        $icon = $this->iconProvider->getSvg( $this->icon, ...$this->attributes->array );

        \assert( $icon instanceof Element );

        if ( $this->size ) {
            // TODO : Add $icon->attributes() to Icon::class
        }

        $this->svg = $icon;

        return parent::getParameters();
    }
}
