<?php

declare(strict_types=1);

namespace Core\View;

use Core\View\ComponentFactory\ViewComponent;
use InvalidArgumentException;
use RuntimeException;

#[ViewComponent( 'icon:{get}:{size}' )]
final class IconComponent extends Component
{
    public Element $svg;

    public function __construct( private readonly IconProviderService $iconProvider ) {}

    /**
     * @param string $get
     * @param ?int   $size
     * @param mixed  ...$attributes
     *
     * @return $this
     */
    public function __invoke(
        ?string  $get = null,
        ?int     $size = null,
        mixed ...$attributes,
    ) : self {
        if ( ! $get ) {
            $this->logger?->error( $this::class.': No icon key provided.' );
            throw new InvalidArgumentException( 'No icon key provided.' );
        }
        $this->svg = $this->iconProvider->getSvg( $get, ...$attributes )
                     ?? throw new RuntimeException( 'No icon found.' );

        dump( [__METHOD__ => $this, ...\get_defined_vars()] );
        return $this;
    }
}
