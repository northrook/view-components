<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\View\Attribute\ViewComponent;
use Core\View\IconView;
use Core\View\Interface\IconProviderInterface;
use Northrook\Logger\Log;
use Core\View\Html\{Attributes};

#[ViewComponent( 'icon:{icon}', true, 128 )]
final class Icon extends AbstractComponent
{
    protected ?string $icon;

    public function __construct( private readonly IconProviderInterface $iconProvider ) {}

    protected function render() : string
    {
        if ( ! $this->icon ) {
            Log::error( $this::class.': No icon key provided.' );
            return '';
        }

        $icon = $this->iconProvider->get( $this->icon );

        if ( ! $icon?->isValid ) {
            return '';
        }

        return $this::view( $icon, $this->attributes );
    }

    /**
     * @param IconView|string                                                     $svg
     * @param array<string, null|array<array-key, string>|bool|string>|Attributes $attributes
     *
     * @return string
     */
    public static function view(
        string|IconView  $svg,
        array|Attributes $attributes = [],
    ) : string {
        return '<i'.Attributes::from( $attributes ).'>'.(string) $svg.'</i>';
    }
}
