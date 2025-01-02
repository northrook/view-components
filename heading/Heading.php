<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\View\Attribute\ViewComponent;
use Stringable;
use Core\View\Html\{Attributes, Tag};

#[ViewComponent( Tag::HEADING, true, 128 )]
final class Heading extends AbstractComponent
{
    use InnerContent;

    public Tag $tag;

    public function __construct()
    {
        $this->tag = Tag::from( 'h1' );
    }

    protected function render() : string
    {
        dump( $this );

        return '<heading>This will be a heading</heading>';
    }

    // /**
    //  * @param 'h1'|'h2'|'h3'|'h4'|'h5'|'h6'|'hgroup'                              $tag
    //  * @param string                                                              $heading
    //  * @param ?string                                                             $subheading
    //  * @param array<string, null|array<array-key, string>|bool|string>|Attributes $attributes
    //  * @param string|Stringable[]                                                 $content
    //  *
    //  * @return string
    //  */
    // public static function view(
    //     string           $tag,
    //     string           $heading,
    //     ?string          $subheading = null,
    //     array|Attributes $attributes = [],
    // ) : string {
    //     return '<i'.Attributes::from( $attributes ).'>'.(string) $svg.'</i>';
    // }
}
