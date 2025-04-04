<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\Asset\ImageAsset;
use Core\AssetManager;
use Core\View\Attribute\ViewComponent;
use Core\View\Template\Component;
use Core\View\Template\Runtime\Html;
use Core\View\Template\Runtime\HtmlStringable;

#[ViewComponent( ['img', 'img:{type}'], true, 60 )]
final class ImageComponent extends Component
{
    public ?string $source = null;

    public ?string $type = null;

    public readonly ImageAsset $image;

    public function __construct( private readonly AssetManager $assetManager ) {}

    protected function resolveAsset() : void
    {
        if ( isset( $this->image ) ) {
            return;
        }

        $imageAsset = $this->assetManager->getAsset( $this->source );

        \assert( $imageAsset instanceof ImageAsset );

        $this->image = $imageAsset;
    }

    protected function prepareArguments( array &$arguments ) : void
    {
        if ( \is_string( $arguments['attributes']['src'] ?? null ) ) {
            $this->source = $arguments['attributes']['src'];
            unset( $arguments['attributes']['src'] );
        }
    }

    protected function getTemplateParameters() : self
    {
        return $this;
    }

    // private function getImage() : HtmlStringable
    // {
    //     $imageAsset = $this->assetManager->getAsset( $this->source );
    //     // var_dump( $this->image );
    //     return new Html(
    //             $imageAsset->getPicture(
    //             // component_id : $this->uniqueID,
    //             ),
    //     );
    // }
    //
    // /**
    //  * @return array{src: string}
    //  */
    // protected function getTemplateParameters() : array
    // {
    //     return [
    //             'image'  => $this->getImage(),
    //             'source' => $this->source ?? 'lamb source',
    //     ];
    // }
}
