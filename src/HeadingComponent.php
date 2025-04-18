<?php

namespace Core\View;

// Subheading
// Heading
// Level
//
// : Role
// . Block - [default] the entire <heading> is wrapped in a <h#> tag
// . Subheading - only the <subheading> is <h#> heading is <span>
// . Heading - only the inner text is <h#>, subheading is <span>
use Core\View\ComponentFactory\ViewComponent;
use Core\View\Element\Tag;
use const Support\TAG_HEADING;

/**
 * # Headings
 *
 * - https://developer.mozilla.org/en-US/docs/Web/HTML/Element/hgroup
 * - https://css-tricks.com/html-for-subheadings-and-headings/
 *
 * We have three different Roles:
 * - Block - [default] the entire `<heading>` is wrapped in a `<h#>` tag
 * - Heading - only the `subheading` is `<h#>`
 * - Subheading - only the `heading` is `<h#>`
 *
 * Subheadings within `<h#>` can be `<small>`, `<span>`, or `<sup>`.
 *
 * - Order can be defined
 * - Subheading tag
 *
 * ```
 * // BLOCK
 * <h1>
 *     <span>This a heading</span>
 *     <small>With a sub-heading</small>
 * </h1>
 *
 * // HEADING|SUBHEADING
 * <hgroup>
 *     <h1>This a heading</h1>
 *     <p>With a sub-heading</p>
 * </hgroup>
 * ```
 */
#[ViewComponent( TAG_HEADING, true )]
final class HeadingComponent extends Component {}
