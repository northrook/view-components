<?php

declare(strict_types=1);

namespace Core\View\Component;

// Each Anchor component must have a unique, fixed id.
// This ID should be saved in a database, and be matched with an in-template pattern should the cache be cleared
// The idea is to have each in-code anchor tag be editable from the back-end
use Core\View\Attribute\ViewComponent;
use Core\View\Html\Element;

/**
 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/a MDN
 */
// #[ViewComponent( tag : 'a', priority : 64 )]
final class AnchorComponent extends AbstractComponent
{
    /**
     * @param ?string                 $set
     * @param array<array-key, mixed> $attributes
     *
     * @return $this
     */
    public function setHref( ?string $set, array &$attributes ) : self
    {
        if ( ! $set && \is_string( $attributes['href'] ?? null ) ) {
            $set = $attributes['href'];
            unset( $attributes['href'] );
        }
        else {
            $set = '#';
        }

        // TODO : Validate schema://example.com
        // TODO : parse mailto:, tel:, sms:, etc
        // TODO : handle executable prefix javascript:url.tld
        // TODO : hreflang
        // TODO : sniff rel=referrerPolicy
        // TODO : sniff _target
        // TODO : sniff type
        // TODO : sniff name|id

        $this->attributes->set( 'href', $set );

        return $this;
    }

    public function primary() : void
    {
        $this->attributes->class->add( 'primary' );
    }

    public function secondary() : void
    {
        $this->attributes->class->add( 'secondary' );
    }

    protected function render() : string
    {
        return (string) new Element(
            'a',
            $this->attributes,
            __METHOD__,
        );
    }
}
