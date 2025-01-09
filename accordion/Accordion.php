<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\View\Html\{Attributes, Content};
use Core\View\Attribute\ViewComponent;
use Core\View\Html\Element\{Heading, Span};
use Support\Normalize;

/**
 * https://www.nngroup.com/articles/accordion-icons/
 */
#[ViewComponent( 'accordion', true, 128 )]
final class Accordion extends AbstractComponent
{
    protected function render() : string
    {
        return $this::view(
            'Accordion title',
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        );
    }

    /**
     * @param Heading|Span|string                                                 $title
     * @param Content|string                                                      $content
     * @param bool                                                                $open
     * @param null|string                                                         $icon
     * @param array<string, null|array<array-key, string>|bool|string>|Attributes $attributes
     *
     * @return string
     */
    public static function view(
        string|Span|Heading $title,
        string|Content      $content,
        bool                $open = false,
        ?string             $icon = null,
        array|Attributes    $attributes = [],
    ) : string {
        $attributes = Attributes::from( $attributes );

        if ( \is_string( $title ) ) {
            $title = new Span( [], $title );
        }

        if ( ! $content instanceof Content ) {
            $content = new Content( $content );
        }

        if ( ! $title instanceof Heading ) {
            $title->attributes->set( 'role', 'heading' );
        }

        if ( ! $attributes->has( 'id' ) ) {
            $attributes->set( 'id', Normalize::key( (string) $title->content.(string) $content ) );
        }

        $state     = $open ? 'true' : 'false';
        $ariaID    = \hash( algo : 'xxh3', data : 'accordion'.(string) $attributes->get( 'id' ) );
        $buttonID  = "{$ariaID}-button";
        $sectionID = "{$ariaID}-section";

        $attributes->class->add( 'flex col' );

        return <<<HTML
            <accordion{$attributes}>
              <button id="{$buttonID}" aria-controls="{$sectionID}" aria-expanded="{$state}" class="pv-xs">{$icon}{$title}</button>
              <section id="{$sectionID}" aria-labelledby="{$buttonID}">{$content}</section>
            </accordion>
            HTML;
    }
}
