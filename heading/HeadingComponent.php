<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\View\Attribute\ViewComponent;
use Core\View\Html\{Attributes, Element, HtmlNode, Tag};

/**
 * Headings
 *
 * - https://developer.mozilla.org/en-US/docs/Web/HTML/Element/hgroup
 * - https://css-tricks.com/html-for-subheadings-and-headings/
 *
 * Seems like there are several ways we could solve the sub-heading component.
 *
 * We need to test how screen headers handle each.
 *
 * ```
 * // We could use <small>, <span>, or <sup> inside  the H tag.
 * <h1>
 *     This a heading
 *     <small>With a sub-heading</small>
 * </h1>
 *
 * // hGroup seems to be the official way in HTML5, but only permits H1-H6 and P.
 * <hgroup>
 *     <h1>This a heading</h1>
 *     <p>With a sub-heading</p>
 * </hgroup>
 *
 * // Lastly, we could use the <header> tag.
 * <header>
 *     <h1>This a heading</h1>
 *     <span>With a sub-heading</span>
 * </header>
 * ```
 *
 * I'm personally leaning towards the regular <h1> tag.
 * It may be worthwhile having an option to include the sub-heading in the
 * heading or not, would need more validation.
 */
#[ViewComponent( Tag::HEADING, true, 128 )]
final class HeadingComponent extends AbstractComponent
{
    use InnerContent;

    public Tag $tag;

    public function __construct()
    {
        $this->tag = Tag::from( 'h1' );
    }

    protected function render() : string
    {
        $this->attributes->class->add( 'heading', true );
        $view = new Element(
            $this->tag,
            $this->attributes,
            ...$this->innerContent->getArray(),
        );

        return $view->render();
    }

    public static function view(
        string|int       $level,
        string           $heading,
        ?string          $subheading = null,
        bool             $subheadingBefore = false,
        bool             $hGroup = false,
        array|Attributes $attributes = [],
    ) {
        $level = Element\Heading::validLevel( $level );

        $heading = new Element(
            $hGroup ? "h{$level}" : 'span',
            HtmlNode::extractAttributes( $heading, true ),
            HtmlNode::unwrap( $heading ),
        );

        if ( $subheading ) {
            $subheading = new Element(
                $hGroup ? 'p' : 'small',
                HtmlNode::extractAttributes( $subheading, true, true ),
                HtmlNode::unwrap( $subheading ),
            );
        }

        $view = new Element(
            $hGroup ? 'hgroup' : "h{$level}",
            $attributes,
            $heading,
            $subheading,
        );

        if ( $subheadingBefore ) {
            $view->attributes->class->add( ['subheading-before'], true );
        }

        $view->attributes->class->add( ['heading'], true );

        return $view->render();
    }
}
