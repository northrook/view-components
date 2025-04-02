<?php

declare(strict_types=1);

namespace Core\View\Component;

use Core\Asset\ImageAsset;
use Core\AssetManager;
use Core\View\Attribute\ViewComponent;
use Core\View\Template\{Compiler\Nodes\Html\ElementNode, Compiler\Position, Component};
use Core\View\Template\Compiler\Node;
use RuntimeException;

#[ViewComponent( ['img', 'img:{type}'], true, 60 )]
final class ImageComponent extends Component
{
    protected string $source;

    protected ?string $type = null;

    public readonly ImageAsset $asset;

    public function __construct( private readonly AssetManager $assetManager ) {}

    // public function getElementNode( ?Position $position = null, ?ElementNode $parent = null ) : ElementNode
    // {
    //     $node = $this->getComponentNode();
    //
    //     // if ( ! $node instanceof ElementNode ) {
    //     //     dd( \get_defined_vars(), $this );
    //     //     throw new RuntimeException();
    //     // }
    //     //
    //     // $node->parent   = $parent;
    //     $node->position = $position;
    //
    //     return $node;
    // }

    // protected function getNode() : Node
    // {
    //     $engine = $this->getEngine();
    //
    //     // $template = $engine->createTemplate(
    //     //     'component/image.latte',
    //     //     ['src' => $this->source],
    //     // );
    //
    //     $template = 'component/image.latte';
    //     // $template = $this->template();
    //
    //     $ast = $engine->parse( $template );
    //
    //     $engine->applyPasses( $ast );
    //
    //     $string = $engine->generate( $ast, $template );
    //     dump( \get_defined_vars() );
    //     return $ast->main;
    // }

    // private function template() : string
    // {
    //     return /** @lang Latte */ <<<'LATTE'
    //         <figure>
    //             <img src="{$src ?? '#'}">
    //             <figcaption>
    //                 Caption Text
    //             </figcaption>
    //         </figure>
    //         LATTE;
    // }

    protected function resolveAsset() : void
    {
        if ( isset( $this->asset ) ) {
            return;
        }

        $imageAsset = $this->assetManager->getAsset( $this->source );

        \assert( $imageAsset instanceof ImageAsset );

        $this->asset = $imageAsset;
    }

    protected function prepareArguments( array &$arguments ) : void
    {
        if ( \is_string( $arguments['attributes']['src'] ?? null ) ) {
            $this->source = $arguments['attributes']['src'];
            unset( $arguments['attributes']['src'] );
        }
    }

    protected function getTemplateParameters() : array
    {
        return ['src' => $this->source];
    }
}
