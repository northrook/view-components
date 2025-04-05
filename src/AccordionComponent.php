<?php

declare(strict_types=1);

namespace Core\View;

use Core\View\Attribute\ViewComponent;
use Core\View\Element\{Attributes, Content, Tag};
use Core\View\Template\{Component};
use function Support\slug;

/**
 * https://www.nngroup.com/articles/accordion-icons/
 */
#[ViewComponent( ['accordion', 'accordion:{icon}'] )]
final class AccordionComponent extends Component
{
    public ?string $icon = null;

    // public function getView() : Element
    // {
    //     return $this::view(
    //         'Accordion title',
    //         'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
    //     );
    // }
    //
    // /**
    //  * @param Element|string                                                      $title
    //  * @param Content|string                                                      $content
    //  * @param bool                                                                $open
    //  * @param null|string                                                         $icon
    //  * @param array<string, null|array<array-key, string>|bool|string>|Attributes $attributes
    //  *
    //  * @return Element
    //  */
    // public static function view(
    //     string|Element   $title,
    //     string|Content   $content,
    //     bool             $open = false,
    //     ?string          $icon = null,
    //     array|Attributes $attributes = [],
    // ) : Element {
    //     $view = new Element(
    //         tag        : 'accordion',
    //         attributes : $attributes,
    //     );
    //
    //     if ( \is_string( $title ) ) {
    //         $title = new Element( 'span', content : $title );
    //     }
    //
    //     if ( ! $content instanceof Content ) {
    //         $content = new Content( $content );
    //     }
    //
    //     if ( ! Tag::isHeading( $title->tag ) ) {
    //         $title->attributes->set( 'role', 'heading' );
    //     }
    //
    //     if ( ! $view->attributes->has( 'id' ) ) {
    //         $view->attributes->set( 'id', slug( (string) $title->content.(string) $content ) );
    //     }
    //
    //     $state     = $open ? 'true' : 'false';
    //     $ariaID    = \hash( algo : 'xxh3', data : 'accordion'.(string) $view->attributes->get( 'id' ) );
    //     $buttonID  = "{$ariaID}-button";
    //     $sectionID = "{$ariaID}-section";
    //
    //     $view->attributes->class->add( 'flex col' );
    //
    //     $button = <<<HTML
    //         <button id="{$buttonID}" aria-controls="{$sectionID}" aria-expanded="{$state}" class="pv-xs">{$icon}{$title}</button>
    //         HTML;
    //
    //     $section = <<<HTML
    //         <section id="{$sectionID}" aria-labelledby="{$buttonID}">{$content}</section>
    //         HTML;
    //
    //     $view->content->set( 'button', $button );
    //     $view->content->set( 'section', $section );
    //
    //     return $view;
    // }
}
