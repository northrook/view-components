<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\View\Attribute\ViewComponent;
use Core\View\Template\ViewElement;
use Core\View\Html\Element\{Heading, Span};
use Core\View\Html\{Attributes, Content};
use Support\Normalize;

/**
 * https://www.nngroup.com/articles/accordion-icons/
 */
#[ViewComponent( 'accordion', true, 128 )]
final class AccordionComponent extends AbstractComponent
{
    public function getView() : ViewElement
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
     * @return ViewElement
     */
    public static function view(
        string|Span|Heading $title,
        string|Content      $content,
        bool                $open = false,
        ?string             $icon = null,
        array|Attributes    $attributes = [],
    ) : ViewElement {
        $view = new ViewElement(
            tag        : 'accordion',
            attributes : $attributes,
        );

        if ( \is_string( $title ) ) {
            $title = new Span( [], $title );
        }

        if ( ! $content instanceof Content ) {
            $content = new Content( $content );
        }

        if ( ! $title instanceof Heading ) {
            $title->attributes->set( 'role', 'heading' );
        }

        if ( ! $view->attributes->has( 'id' ) ) {
            $view->attributes->set( 'id', Normalize::key( (string) $title->content.(string) $content ) );
        }

        $state     = $open ? 'true' : 'false';
        $ariaID    = \hash( algo : 'xxh3', data : 'accordion'.(string) $view->attributes->get( 'id' ) );
        $buttonID  = "{$ariaID}-button";
        $sectionID = "{$ariaID}-section";

        $view->attributes->class->add( 'flex col' );

        $button = <<<HTML
            <button id="{$buttonID}" aria-controls="{$sectionID}" aria-expanded="{$state}" class="pv-xs">{$icon}{$title}</button>
            HTML;

        $section = <<<HTML
            <section id="{$sectionID}" aria-labelledby="{$buttonID}">{$content}</section>
            HTML;

        $view->content->set( 'button', $button );
        $view->content->set( 'section', $section );

        return $view;
    }
}
