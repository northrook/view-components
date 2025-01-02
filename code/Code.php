<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\View\Attribute\ViewComponent;
use Core\View\Html\{Element, Tag};
use Northrook\Logger\Log;
use Support\Str;
use Tempest\Highlight\Highlighter;

#[ViewComponent( ['pre', 'code:{language}:block'], true, -256 )]
final class Code extends AbstractComponent
{
    use InnerContent;

    protected bool $tidy = false;

    protected ?string $language = null;

    private bool $block = false;

    private string $code;

    public Tag $tag;

    public function __construct()
    {
        $this->tag = Tag::from( 'code' );
    }

    protected function render() : string
    {
        if ( ! $this->block || ! isset( $this->code ) ) {
            $this->inlineCode();
        }
        if ( $this->tidy ) {
            $this->code = (string) Str::replaceEach(
                [' ), );' => ' ) );'],
                $this->code,
            );
        }

        if ( $this->language ) {
            $content = "{$this->highlight( $this->code )}";
            $lines   = \substr_count( $content, PHP_EOL );
            // $this->attributes( 'language', $this->language );

            // if ( $lines ) {
            //     $this->attributes( 'line-count', (string) $lines );
            // }
        }
        else {
            $content = $this->code;
        }

        $view = new Element(
            $this->tag,
            $this->attributes,
            ...$this->innerContent->getArray(),
        );

        return $view->render();
    }

    private function getCode() : string
    {
        return $this->code = $this->innerContent->getString();
    }

    protected function inlineCode() : void
    {
        $this->code = (string) \preg_replace( '#\s+#', '', $this->getCode() );
    }

    protected function block() : void
    {
        $leftPadding = [];
        $lines       = \explode( "\n", $this->getCode() );

        foreach ( $lines as $line ) {
            $line = \str_replace( "\t", '    ', $line );
            if ( \preg_match( '#^(\s+)#m', $line, $matches ) ) {
                $leftPadding[] = \strlen( $matches[0] );
            }
        }

        $trimSpaces = \min( $leftPadding );

        foreach ( $lines as $line => $string ) {
            if ( \str_starts_with( $string, \str_repeat( ' ', $trimSpaces ) ) ) {
                $string = \substr( $string, $trimSpaces );
            }

            \preg_match( '#^(\s*)#m', $string, $matches );
            $leftPad      = \strlen( $matches[0] ?? 0 );
            $string       = \str_repeat( ' ', $leftPad ).\trim( $string );
            $lines[$line] = \str_replace( '    ', "\t", $string );
        }

        $this->code = \implode( "\n", $lines );
        $this->tag->set( 'pre' );
        $this->attributes->class->add( 'block', true );
        $this->block = true;
    }

    final protected function highlight( string $code, ?int $gutter = null ) : string
    {
        if ( ! $this->language || ! $code ) {
            return '';
        }

        if ( ! $this->block && $gutter ) {
            Log::warning( 'Inline code snippets cannot have a gutter' );
            $gutter = null;
        }

        $highlighter = new Highlighter();
        if ( $gutter ) {
            return $highlighter->withGutter( $gutter )->parse( $code, $this->language );
        }
        return $highlighter->parse( $code, $this->language );
    }
}

// protected function parseArguments( array &$arguments ) : void
// {
//     if ( 'pre' === $arguments['tag'] ) {
//         $arguments[] = 'block';
//     }
//     $content = $arguments['content'] ?? [];
//
//     if ( ! \array_is_list( $content ) ) {
//         $content = HtmlContent::toArray( $content );
//     }
//
//     foreach ( $content as $index => $value ) {
//         if ( \is_array( $value ) ) {
//             // $value = HtmlContent::contentString( [$value]);
//             // dump( [ $value ] );
//             continue;
//         }
//
//         if ( ! \is_string( $value ) ) {
//             dump( __METHOD__.' encountered invalid content value.', $this, '---' );
//
//             continue;
//         }
//
//         if ( ! \trim( $value ) ) {
//             unset( $content[$index] );
//         }
//     }
//     $this->code = \implode( '', $content );
//
//     unset( $arguments['content'] );
// }
