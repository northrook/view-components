<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\View\Attribute\ViewComponent;
use Core\View\Element;
use Core\View\Element\{Attributes, Tag};
use Core\View\Html\HtmlNode;
use Stringable;
use Support\Str;

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
 * heading or not, would need more validation.å
 */
#[ViewComponent( Tag::HEADING, true, 128 )]
final class HeadingComponent extends AbstractComponent
{
    public function getView() : Element
    {
        $this->attributes->class->add( 'heading', true );

        $heading          = [];
        $subheading       = null;
        $subheadingBefore = false;
        $prepend          = [];
        $append           = [];

        $innerContent = $this->innerContent->getArray();

        foreach ( $innerContent as $key => $value ) {
            if ( \is_string( $key ) ) {
                if ( ! $subheading && Str::startsWith( $key, ['small', 'p'] ) ) {
                    $subheadingBefore = \array_key_first( $innerContent ) === $key;
                    $subheading       = $value;

                    continue;
                }
                if ( Str::startsWith( $key, ['div', 'i', 'svg', 'picture', 'img', 'canvas', 'video'] ) ) {
                    if ( ! $heading ) {
                        $prepend[] = $value;
                    }
                    else {
                        $append[] = $value;
                    }

                    continue;
                }
            }
            $heading[] = $value;
        }

        $heading = \implode( ' ', $heading );
        $prepend = \implode( ' ', $prepend ) ?: null;
        $append  = \implode( ' ', $append ) ?: null;

        // dd( $heading, $subheading, $subheadingBefore, $prepend, $append );

        return $this::view(
            level            : $this->view->tag->getTagName(),
            heading          : $heading,
            subheading       : $subheading,
            subheadingBefore : $subheadingBefore,
            prependHtml      : $prepend,
            appendHtml       : $append,
            attributes       : $this->attributes,
        );
    }

    /**
     * @param int|string                                                             $level
     * @param string|Stringable                                                      $heading
     * @param null|string|Stringable                                                 $subheading
     * @param bool                                                                   $subheadingBefore
     * @param bool                                                                   $hGroup
     * @param null|string|Stringable                                                 $prependHtml
     * @param null|string|Stringable                                                 $appendHtml
     * @param array<array-key, null|array<array-key, string>|bool|string>|Attributes $attributes
     *
     * @return Element
     */
    public static function view(
        string|int             $level,
        string|Stringable      $heading,
        null|string|Stringable $subheading = null,
        bool                   $subheadingBefore = false,
        bool                   $hGroup = false,
        null|string|Stringable $prependHtml = null,
        null|string|Stringable $appendHtml = null,
        array|Attributes       $attributes = [],
    ) : Element {
        $level = Element\Heading::validLevel( $level );

        $heading = new Element(
            $hGroup ? "h{$level}" : 'span',
            HtmlNode::extractAttributes( $heading, true, true ),
            HtmlNode::unwrap( $heading ),
        );

        if ( $subheading ) {
            $subheading = new Element(
                $hGroup ? 'p' : 'small',
                HtmlNode::extractAttributes( $subheading, true, true ),
                HtmlNode::unwrap( $subheading ),
            );

            $subheading->attributes->class->add( 'subheading', true );
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

        if ( $prependHtml ) {
            $view->content->prepend( $prependHtml );
        }

        if ( $appendHtml ) {
            $view->content->append( $appendHtml );
        }

        return $view;
    }
}
