<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\View\Attribute\ViewComponent;
use Core\View\Html\Attributes;
use Core\View\Template\ViewElement;
use Tempest\Highlight\Language;
use Support\{Highlight, Str};
use const Support\AUTO;
use Stringable;

#[ViewComponent( ['pre', 'code', 'pre:{language}', 'code:{language}:{block}'], true, -256 )]
final class CodeComponent extends AbstractComponent
{
    protected bool $tidy = false;

    protected null|string|false $language = null;

    protected bool $block = false;

    protected ?int $gutter = null;

    protected string $code;

    public function getView() : ViewElement
    {
        $this->code = $this->innerContent->getString();
        // dump( $this );

        // if ( !$this->code ) {
        //     throw new InvalidArgumentException( $this::class . " was provided an empty 'code' property." );
        // }

        if ( $this->view->tag->is( 'pre' ) ) {
            $this->block = true;
        }

        return $this::view(
            code       : $this->code,
            block      : $this->block,
            language   : $this->language,
            tidy       : $this->tidy,
            attributes : $this->view->attributes,
        );
    }

    /**
     * @param string                                                              $code
     * @param bool                                                                $block      [false] Inline `<code>` by default
     * @param null|false|Language|string                                          $language
     * @param null|int                                                            $gutter
     * @param bool                                                                $tidy
     * @param array<string, null|array<array-key, string>|bool|string>|Attributes $attributes
     *
     * @return ViewElement
     */
    public static function view(
        string                     $code,
        bool                       $block = false,
        false|null|string|Language $language = AUTO,
        ?int                       $gutter = null,
        bool                       $tidy = false,
        array|Attributes           $attributes = [],
    ) : ViewElement {
        $view = new ViewElement(
            $block ? 'pre' : 'code',
            $attributes,
        );

        $code = $block ? self::codeBlock( $code ) : self::codeInline( $code );

        if ( $tidy ) {
            $code = Str::replaceEach(
                [' ), );' => ' ) );'],
                $code,
            );
            /** @var string $code */
        }

        if ( false !== $language ) {
            $highlight = new Highlight( $code, $language, $gutter );
            $lines     = \substr_count( $code, PHP_EOL );
            if ( $lines ) {
                $view->attributes->add( 'code-lines', $lines + 1 );
            }
            $view->attributes->add( 'code-language', $highlight->language->getName() );
            $code = $highlight->__toString();
        }

        $view->content( $code );

        return $view;
    }

    final protected static function codeInline( string|Stringable $code ) : string
    {
        return (string) \preg_replace( '#\s+#', ' ', (string) $code );
    }

    final protected static function codeBlock( string|Stringable $code ) : string
    {
        $leftPadding = [];
        $lines       = \explode( "\n", (string) $code );

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
