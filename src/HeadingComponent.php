<?php

declare(strict_types=1);

namespace Core\View;

// Subheading
// Heading
// Level
//
// : Role
// . Block - [default] the entire <heading> is wrapped in a <h#> tag
// . Subheading - only the <subheading> is <h#> heading is <span>
// . Heading - only the inner text is <h#>, subheading is <span>

use Core\View\Component\Arguments;
use Core\View\ComponentFactory\ViewComponent;

use Core\View\Element\Heading;
use const Support\{AUTO, TAG_HEADING};

/**
 * # Headings
 *
 * - https://developer.mozilla.org/en-US/docs/Web/HTML/Element/hgroup
 * - https://css-tricks.com/html-for-subheadings-and-headings/
 *
 * We have three different Roles:
 * - Block - [default] the entire `<heading>` is wrapped in a `<h#>` tag
 * - Heading - only the `subheading` is `<h#>`
 * - Subheading - only the `heading` is `<h#>`
 *
 * Subheadings within `<h#>` can be `<small>`, `<span>`, or `<sup>`.
 *
 * - Order can be defined
 * - Subheading tag
 *
 * ```
 * // BLOCK
 * <h1>
 *     <span>This is a heading</span>
 *     <small>With a sub-heading</small>
 * </h1>
 *
 * // HEADING|SUBHEADING
 * <hgroup>
 *     <h1>This a heading</h1>
 *     <p>With a sub-heading</p>
 * </hgroup>
 * ```
 */
#[ViewComponent( TAG_HEADING )]
final class HeadingComponent extends Component
{
    public readonly Heading $heading;

    /**
     * @param null|string          $heading
     * @param null|string          $subheading
     * @param int                  $level
     * @param null|bool            $subheadingFirst
     * @param string               $type
     * @param array<string, mixed> $attributes
     *
     * @return self
     */
    public function __invoke(
        string  $heading,
        ?string $subheading = null,
        int     $level = 1,
        ?bool   $subheadingFirst = AUTO,
        string  $type = Heading::TYPE_GROUP,
    ) : self {
        $this->heading = new Heading(
            $heading,
            $subheading,
            $level,
            $subheadingFirst,
            $type,
        );
        return $this;
    }

    public static function prepareArguments( Arguments $arguments ) : void
    {
        dump( $arguments );
    }

    protected function getString() : string
    {
        return $this->heading->render();
    }
}
