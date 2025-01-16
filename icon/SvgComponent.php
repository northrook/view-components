<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\View\Attribute\ViewComponent;
use Core\View\IconView;
use Core\View\Interface\IconProviderInterface;
use Northrook\Logger\Log;

#[ViewComponent( 'svg:{icon}', true, 144 )]
final class SvgComponent extends AbstractComponent
{
    protected ?string $icon;

    public function __construct( private readonly IconProviderInterface $iconProvider ) {}

    public function getView() : IconView
    {
        $icon = $this->iconProvider->get( $this->icon ?? '' );

        if ( ! $icon?->isValid ) {
            Log::error( $this::class.': No icon key provided.' );
            $icon = '';
        }

        $icon->attributes->merge( $icon->attributes );

        return $icon;
    }
}
