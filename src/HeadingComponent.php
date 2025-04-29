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

use Core\View\Element\{Attributes, Content, Heading};
use Core\View\Exception\{ViewException};
use InvalidArgumentException;
use DOMDocument;
use DOMText;
use Exception;
use DOMElement;
use const Support\{AUTO, TAG_HEADING, TAG_INLINE};

const TAG_SUBHEADING = ['small', 'span', 'sup', 'subheading'];

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
    public string $tag;

    public readonly Content $content;

    public static function prepareArguments( Arguments $arguments ) : void
    {
        $level = null;
        $type  = Heading::TYPE_GROUP;
        if ( $arguments->node->name === 'hgroup' ) {
            foreach ( $arguments->node->getContent() as $node ) {
                if ( $level ) {
                    throw new InvalidArgumentException( 'Heading level cannot be defined more than once.' );
                }

                $name = $node->name ?? throw new InvalidArgumentException(
                    "Unknown node type '".$node::class."'.",
                );

                if ( \in_array(
                    $name,
                    ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                ) ) {
                    $type  = Heading::TYPE_HEADING;
                    $level = (int) $name[1];
                }
            }
        }
        elseif ( \in_array(
            $arguments->node->name,
            ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        ) ) {
            $level = (int) $arguments->node->name[1];
        }
        else {
            throw new InvalidArgumentException( 'Unable to determine heading level.' );
        }

        // dd( get_defined_vars() );
        $arguments
            ->add( 'heading', $arguments->node->content )
            ->add( 'level', $level )
            ->add( 'type', $type );
        // ->add( 'language', $arguments->attributes->pull( 'lang' ) ?? null )
        // ->add( 'block', $arguments->node->name === 'pre' );
    }

    /**
     * @param string      $heading
     * @param null|string $subheading
     * @param int         $level
     * @param null|bool   $subheadingFirst
     * @param string      $type
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
        $this->tag = $type === Heading::TYPE_GROUP ? "h{$level}" : 'hgroup';

        $this->content = new Content( $this->DOMContent( $heading ) );

        // : If $subheading is provided, treat $heading as given
        // . Else, parse $heading, generating $content

        $this->attributes->class->add( 'heading', true );
        return $this;
    }

    protected function getString() : string
    {
        return (string) new Element( $this->tag, $this->content, $this->attributes );
    }

    /**
     * @param string $html
     *
     * @return array<array-key, Element|string>
     */
    private function DOMContent( string $html ) : array
    {
        $html = \trim( $html );
        if ( ! \str_starts_with( $html, '<span' ) && ! \str_ends_with( $html, '</span>' ) ) {
            $html = "<span>{$html}</span>";
        }

        $heading    = null;
        $subheading = null;

        $fragment = [];
        $content  = [];

        try {
            $dom = new DOMDocument();
            $dom->loadHTML(
                source  : $html,
                options : LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOCDATA,
            );
            $dom->encoding = CHARSET;

            foreach ( $dom->childNodes[0]->childNodes as $node ) {
                // Integer nodes are always inline text
                if ( $node instanceof DOMText ) {
                    $fragment[] = $node->textContent;

                    continue;
                }

                if ( ! $node instanceof DOMElement ) {
                    $this->logger?->error(
                        'Unexpected Node: {node}',
                        ['node' => $node],
                    );

                    continue;
                }

                $tag = $node->nodeName;

                if ( ! $subheading && \in_array( $tag, TAG_SUBHEADING ) ) {
                    $subheading = $dom->saveHTML( $node )
                            ?: throw new ViewException(
                                $this::class,
                                \error_get_last()['message'] ?? null,
                            );

                    $attributes = Attributes::extract( $subheading, true );
                    $attributes->class->add( 'subheading', true );

                    if ( $fragment && $value = \trim( \implode( '', $fragment ) ) ) {
                        $content[] = new Element( 'span', $value );
                    }
                    $content[] = new Element( $tag, $subheading, $attributes );
                    $fragment  = [];
                }
                elseif ( \in_array( $tag, TAG_INLINE ) ) {
                    $fragment[] = $dom->saveHTML( $node );
                }
                else {
                    $content[] = $dom->saveHTML( $node );
                }
            }

            if ( $fragment && $value = \trim( \implode( '', $fragment ) ) ) {
                $content[] = new Element( 'span', $value );
            }
        }
        catch ( Exception $exception ) {
            $this->logger?->error( $exception->getMessage(), ['exception' => $exception, 'component' => $this] );
        }

        // dump(
        //         [
        //                 'content'    => $content,
        //                 'heading'    => $heading,
        //                 'subheading' => $subheading,
        //         ],
        // );
        // return $contentOnly ? \end( $ast )[ 'content' ] : $ast;
        return $content;
    }
}
