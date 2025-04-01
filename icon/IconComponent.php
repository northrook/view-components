<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\Interface\IconProviderInterface;
use Core\View\Attribute\ViewComponent;
use Core\View\Element\Attributes;
use Core\View\{Element, Icon};
use Northrook\Logger\Log;
use Core\View\Template\AbstractComponent;
use InvalidArgumentException;

#[ViewComponent( 'icon:{icon}', true, 142 )]
final class IconComponent extends AbstractComponent
{
    protected ?string $icon;

    public function __construct( private readonly IconProviderInterface $iconProvider ) {}

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
            $icon = '';
        }

        return $this::view( $icon, $this->attributes );
    }

    /**
     * @param Icon|string                                                         $svg
     * @param array<string, null|array<array-key, string>|bool|string>|Attributes $attributes
     *
     * @return Element
     */
    public static function view(
        string|Icon      $svg,
        array|Attributes $attributes = [],
    ) : Element {
        return new Element( 'i', $svg, $attributes );
    }
}
