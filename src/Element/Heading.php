<?php

namespace Core\View\Element;

use Core\View\Element;
use const Support\AUTO;
use Stringable;
use InvalidArgumentException;

final class Heading extends Element
{
    public const string
        TYPE_GROUP      = 'group',
        TYPE_HEADING    = 'heading',
        TYPE_SUBHEADING = 'subheading';

    public const string
        ORDER_HEADING    = 'heading-first',
        ORDER_SUBHEADING = 'subheading-first';

    public readonly Element $heading;

    public readonly Element $subheading;

    /**
     * @param null|string|Stringable $heading
     * @param null|string|Stringable $subheading
     * @param int                    $level
     * @param null|bool              $subheadingFirst
     * @param self::TYPE_*           $type
     */
    public function __construct(
        null|string|Stringable $heading,
        null|string|Stringable $subheading = null,
        public int             $level = 1,
        protected ?bool        $subheadingFirst = AUTO,
        protected string       $type = self::TYPE_GROUP,
    ) {
        parent::__construct();
        $this->heading    = new Element( 'span', $heading );
        $this->subheading = new Element( 'small', $subheading );
    }

    public function subheadingFirst() : self
    {
        $this->subheadingFirst = true;
        return $this;
    }

    public function headingFirst() : self
    {
        $this->subheadingFirst = false;
        return $this;
    }

    /**
     * @param self::ORDER_* $order
     *
     * @return $this
     */
    public function order( string $order ) : self
    {
        $this->subheadingFirst = match ( $order ) {
            self::ORDER_SUBHEADING => true,
            self::ORDER_HEADING    => false,
            default                => throw new InvalidArgumentException(),
        };
        return $this;
    }

    /**
     * @param self::TYPE_* $type
     *
     * @return $this
     */
    public function type( string $type ) : self
    {
        $this->type = match ( $type ) {
            self::TYPE_GROUP,
            self::TYPE_HEADING,
            self::TYPE_SUBHEADING => $type,
            default               => throw new InvalidArgumentException(),
        };
        return $this;
    }

    /**
     * @param int<1,6> $level
     *
     * @return $this
     */
    public function level(
        int $level,
    ) : self {
        $this->level = $level;
        return $this;
    }

    protected function build() : void
    {
        if ( $this->type === self::TYPE_GROUP ) {
            $this->tag( "h{$this->level}" );
        }
        else {
            $this->tag( 'hgroup' );
            $this->heading->tag( "h{$this->level}" );
            $this->subheading->tag( 'p' );
        }

        if ( $this->subheading->content->has() ) {
            $this->content(
                $this->subheadingFirst
                            ? [
                                'subheading' => $this->subheading,
                                'heading'    => $this->heading,
                            ]
                            : [
                                'heading'    => $this->heading,
                                'subheading' => $this->subheading,
                            ],
            );

            return;
        }

        $this->content( ['heading' => $this->heading] );
        // on build, check properties, set content order and tags accordingly
    }
}
