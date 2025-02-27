<?php

declare(strict_types=1);

namespace Core\View\Component;

/*
 # Accessibility
 : https://github.com/WICG/accessible-notifications
 : https://inclusive-components.design/notifications/

    - Don't use aria-atomic="true" on live elements, as it will announce any change within it.
    - Be judicious in your use of visually hidden live regions. Most content should be seen and heard.
    - Distinguish parts of your interface in content or with content and style, but never just with style.
    - Do not announce everything that changes on the page.
    - Be very wary of Desktop notifications, may cause double announcements etc.


 : https://atlassian.design/components/flag/examples
    Used for confirmations, alerts, and acknowledgments
    that require minimal user interaction.

 : https://atlassian.design/components/banner/examples
    Banner displays a prominent message at the top of the screen.

    We may want to create a separate component, or have types
    such as 'floating' using the Toast system, or 'static'
    using fixed positioning 'top|bottom' with left/right/center.

 */

use Core\View\Attribute\ViewComponent;
use Core\View\Element\Attributes;
use Core\View\{Element, IconSet};
use Core\View\Interface\IconProviderInterface;
use Support\Time;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[ViewComponent( 'toast:{status}' )]
final class ToastComponent extends AbstractComponent
{
    public string $id;

    public string $status;

    public string $message;

    public ?string $description = null;

    public ?int $timeout = null;

    public int $timestamp;

    public string $when;

    public ?string $icon = null;

    public bool $compact = false;

    /**
     * @param IconProviderInterface $iconProvider [lazy]
     */
    public function __construct(
        #[Autowire( service : IconSet::class )]
        private readonly IconProviderInterface $iconProvider,
    ) {}

    public function getView() : Element
    {
        return $this::view( 'Hello there.' );
    }

    /**
     * @param array{instances: string[], timestamp: ?int, status: ?string, icon: ?string } $arguments
     *
     * @return void
     */
    protected function prepareArguments( array &$arguments ) : void
    {
        $instances = $arguments['instances']                                               ?? null;
        $dateTime  = $instances ? \array_key_first( $instances ) : $arguments['timestamp'] ?? 'now';

        \assert(
            \is_string( $dateTime ) || \is_int( $dateTime ),
            'Expected string|int, '.\gettype( $dateTime ).' given',
        );

        $timestamp = new Time( $dateTime );

        $this->timestamp = $timestamp->unixTimestamp;
        $this->when      = $timestamp->format( $timestamp::FORMAT_HUMAN, true );

        unset( $arguments['instances'], $arguments['timestamp'] );
    }

    private function details() : string
    {
        if ( $this->description ) {
            return AccordionComponent::view(
                'Details',
                $this->description,
                false,
                null, // $this->iconService->getIcon( 'chevron', [ 'class' => 'direction:right' ] ),
                ['id' => "toast-{$this->id}"],
            )->render();
        }
        return '';
    }

    private function icon() : string
    {
        return (string) $this->iconProvider->get(
            $this->icon ?? $this->status,
            ['height' => '1rem', 'width' => '1rem'],
        );
    }

    // protected function render() : string
    // {
    //     return $this->getView()->render();
    // }

    protected function render() : string
    {
        if ( $this->timeout ) {
            $this->attributes->set( 'timeout', (string) $this->timeout );
        }

        $this->attributes
            ->set( 'id', $this->id )
            ->class->add( "intent:{$this->status}" );

        // Add a 'compact' mode for replacing the status text with message

        if ( $this->compact ) {
            $type    = '<span data-message>'.\trim( $this->message, " \n\r\t\v\0." ).'</span>';
            $message = null;
        }
        else {
            $message = "<span data-message>{$this->message}</span>";
            $type    = "<span data-type>{$this->status}</span>";
        }

        return <<<HTML
            <toast {$this->attributes}>
                <button class="close" aria-label="Close" type="button"></button>
                <output role="status">
                    <i class="status">
                        {$this->icon()}
                        {$type}
                        <time datetime="{$this->timestamp}">{$this->when}</time>
                    </i>
                    {$message}
                </output>
                {$this->details()}
            </toast>
            HTML;
    }

    /**
     * @param string                                                              $message
     * @param ?string                                                             $description
     * @param ?string                                                             $id
     * @param string                                                              $status
     * @param ?int                                                                $timeout
     * @param int                                                                 $timestamp
     * @param array<string, null|array<array-key, string>|bool|string>|Attributes $attributes
     * @param string                                                              $when
     * @param string                                                              $icon
     *
     * @return Element
     */
    public static function view(
        string           $message,
        ?string          $description = null,
        ?string          $id = null, // generate from content hash
        string           $status = 'notice',
        ?int             $timeout = null,
        ?int             $timestamp = null,
        // string           $when,
        // string           $icon,
        array|Attributes $attributes = [],
    ) : Element {
        $timestamp ??= \time();

        $view = new Element( 'toast', $attributes );

        return $view;
    }
}
