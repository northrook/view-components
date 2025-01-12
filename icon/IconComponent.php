<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\View\Attribute\ViewComponent;
use Core\View\Html\Attributes;
use Core\View\IconView;
use Core\View\Interface\IconProviderInterface;
use Core\View\Template\ViewElement;
use Northrook\Logger\Log;

#[ViewComponent( 'icon:{icon}', true, 128 )]
final class IconComponent extends AbstractComponent
{
    protected ?string $icon;

    public function __construct( private readonly IconProviderInterface $iconProvider ) {}

    public function getView() : ViewElement
    {
        $icon = $this->iconProvider->get( $this->icon ?? '' );

        if ( ! $icon?->isValid ) {
            Log::error( $this::class.': No icon key provided.' );
            $icon = '';
        }

        return $this::view( $icon, $this->attributes );
    }

    /**
     * @param IconView|string                                                     $svg
     * @param array<string, null|array<array-key, string>|bool|string>|Attributes $attributes
     *
     * @return ViewElement
     */
    public static function view(
        string|IconView  $svg,
        array|Attributes $attributes = [],
    ) : ViewElement {
        return new ViewElement( 'i', $attributes, $svg );
    }
}
