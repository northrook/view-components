<?php

declare(strict_types=1);

namespace Core\View;

use Core\Interface\View;
use Core\View\ComponentFactory\ViewComponent;
use Core\View\Template\Component;
use Core\View\Template\Runtime\Html;
use Support\Highlight;
use function Support\str_replace_each;
use const Support\AUTO;

#[ViewComponent( ['pre', 'code', 'pre:{language}', 'code:{language}:{block}'] )]
final class CodeComponent extends Component
{
    protected bool $tidy = false;

    protected null|string|false $language = null;

    protected ?bool $block = false;

    protected ?int $gutter = null;

    public string $tag;

    public ?View $code = null;

    /**
     * @param string  $code
     * @param ?string $language
     * @param bool    $block
     * @param ?int    $gutter
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
        dump( \get_defined_vars() );
        return $this;
    }

    protected function onCreation( ?string &$content ) : void
    {
        if ( ! $content ) {
            $this->logger?->warning( 'No code content provided.' );
            return;
        }

        $code = $this->block ? self::codeBlock( $content ) : self::codeInline( $content );

        if ( $this->tidy ) {
            $code = (string) str_replace_each( [' ), );' => ' ) );'], $code );
        }

        if ( $this->language !== false ) {
            $highlight = new Highlight( $code, $this->language, $this->gutter );
            $lines     = \substr_count( $code, PHP_EOL );
            if ( $lines ) {
                $this->attributes->set( 'code-lines', $lines + 1 );
            }
            $this->attributes->set( 'code-language', $highlight->language->getName() );
            $code = $highlight;
        }

        $this->code = new Html( $code );
    }

    protected function prepareArguments(
        array &   $properties,
        array &   $attributes,
        array &   $actions,
        ?string & $content,
    ) : void {
        $tag = $attributes['tag'] ?? 'code';

        $properties['language'] ??= $attributes['lang'] ?? null;
        $properties['block']    ??= $tag === 'pre';

        $content = $content ? ( \trim( $content ) ?: null ) : null;
    }

    protected function getParameters() : array|object
    {
        return parent::getParameters();
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
