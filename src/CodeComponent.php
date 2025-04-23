<?php

declare(strict_types=1);

namespace Core\View;

use Core\View\Component\Arguments;
use Core\View\ComponentFactory\ViewComponent;
use Core\View\Template\Runtime\Html;
use Support\Highlight;
use function Support\str_replace_each;
use const Support\AUTO;

#[ViewComponent( ['pre', 'code', 'pre:{language}', 'code:{language}:{block}'] )]
final class CodeComponent extends Component
{
    public string $tag;

    public readonly null|string|false $language;

    public readonly ?Html $code;

    public static function prepareArguments( Arguments $arguments ) : void
    {
        $arguments
            ->add( 'code', $arguments->node->content )
            ->add( 'language', $arguments->attributes->pull( 'lang' ) ?? null )
            ->add( 'block', $arguments->node->name === 'pre' );
    }

    /**
     * @param string  $code
     * @param ?string $language
     * @param bool    $block
     * @param ?int    $gutter   Show a line number gutter starting from [int]
     * @param bool    $tidy
     *
     * @return $this
     */
    public function __invoke(
        string  $code,
        ?string $language = AUTO,
        bool    $block = false,
        ?int    $gutter = null,
        bool    $tidy = false,
    ) : self {
        $this->tag      = $block ? 'pre' : 'code';
        $this->language = $this->codeLanguage( $language );
        $this->code     = $this->highlightHtml( $code, $block, $gutter, $tidy );
        return $this;
    }

    private function highlightHtml(
        string $code,
        bool   $block,
        ?int   $gutter,
        bool   $tidy,
    ) : ?Html {
        $code = \trim( $code );

        if ( ! $code ) {
            $this->logger?->warning( 'No code content provided.' );
            return null;
        }

        $code = $block ? self::codeBlock( $code ) : self::codeInline( $code );
        dump( \get_defined_vars() );

        if ( $tidy ) {
            $code = (string) str_replace_each( [' ), );' => ' ) );'], $code );
        }

        if ( $this->language !== false ) {
            $highlight = new Highlight( $code, $this->language, $gutter );
            $lines     = \substr_count( $code, PHP_EOL );
            if ( $lines ) {
                $this->attributes->set( 'code-lines', $lines + 1 );
            }
            $this->attributes->set( 'code-language', $highlight->language->getName() );
            $code = $highlight;
        }

        return new Html( $code );
    }

    private function codeLanguage( ?string $language ) : null|string|false
    {
        if ( $language === 'NONE' ) {
            return false;
        }
        return $language;
    }

    final protected function codeInline( string $string ) : string
    {
        return (string) \preg_replace( '#\s+#', ' ', $string );
    }

    final protected static function codeBlock( string $string ) : string
    {
        $leftPadding = [];
        $lines       = \explode( "\n", $string );

        // dump( $lines );

        foreach ( $lines as $line ) {
            $line = \str_replace( "\t", '    ', $line );
            if ( \preg_match( '#^(\s+)#m', $line, $matches ) ) {
                $leftPadding[] = \strlen( $matches[0] );
            }
        }

        $trimSpaces = $leftPadding ? \min( $leftPadding ) : 0;
        // dump( $leftPadding, $trimSpaces );

        foreach ( $lines as $line => $string ) {
            if ( \str_starts_with( $string, \str_repeat( ' ', $trimSpaces ) ) ) {
                $string = \substr( $string, $trimSpaces );
            }

            \preg_match( '#^(\s*)#m', $string, $matches );
            $leftPad = \strlen( $matches[0] ?? '' );
            $string  = \str_repeat( ' ', $leftPad ).\trim( $string );
            // :: Handled by Str::normalize
            $lines[$line] = \str_replace( '    ', "\t", $string );
        }

        return \implode( "\n", $lines );
    }
}
