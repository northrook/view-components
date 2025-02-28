<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\View\Attribute\ViewComponent;
use Core\View\{Element, Icon};
use Core\Interface\IconProviderInterface;
use Northrook\Logger\Log;
use InvalidArgumentException;

#[ViewComponent( 'svg:{icon}', true, 144 )]
final class SvgComponent extends AbstractComponent
{
    protected ?string $icon;

    public function __construct( private readonly IconProviderInterface $iconProvider ) {}

    /**
     * @return Element<Icon>
     */
    public function getView() : Element
    {
        $icon = $this->iconProvider->get( $this->icon ?? '' );

        if ( ! $icon ) {
            Log::error( $this::class.': No icon key provided.' );
            throw new InvalidArgumentException( 'No icon key provided.' );
        }

        \assert( $icon instanceof Icon );

        if ( ! $icon->isValid ) {
            Log::error( $this::class.': No icon key provided.' );
            throw new InvalidArgumentException( 'No icon key provided.' );
        }

        $icon->attributes->merge( $icon->attributes );

        return $icon;
    }
}
