<?php
/**
 * Server-side rendering of the `core/cover` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/cover` block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the cover block markup, if useFeaturedImage is true.
 */
function render_block_core_cover( $attributes, $content ) {
	if( false === $attributes['useFeaturedImage'] ) {
		return $content;
	}

	$currentFeaturedImage = get_the_post_thumbnail_url();

	if( false === $currentFeaturedImage ) {
		return $content;
	}

	$isImgElement = ! ( $attributes['hasParallax'] || $attributes['isRepeated'] );
	$isImageBackground = $attributes['backgroundType'] === 'image';

	if( $isImageBackground && ! $isImgElement ) {
		$content = preg_replace(
			'/class=\".*?\"/',
			'${0} style="background-image:url(' . esc_url( $currentFeaturedImage ) . ')"',
			$content,
			1
		);
	}

	if( $isImageBackground && $isImgElement ) {
		$objectPosition = '';
		if ( isset( $attributes['focalPoint'] ) ) {
			$objectPosition = round( $attributes['focalPoint']['x'] * 100 ) . '%'. ' ' .
			round( $attributes['focalPoint']['x'] * 100 ) . '%';
		}


		$image_template = '<img
			class="wp-block-cover__image-background"
			alt="%s"
			src="%s"
			style="object-position: %s"
			data-object-fit="cover"
			data-object-position="%s"
		/>';

		$image = sprintf(
			$image_template,
			esc_attr( get_the_post_thumbnail_caption() ),
			esc_url( $currentFeaturedImage ),
			esc_attr( $objectPosition ),
			esc_attr( $objectPosition )
		);

		$content = str_replace(
			'</span><div',
			'</span>' . $image . '<div',
			$content,
		);

	}

	return $content;
}

/**
 * Registers the `core/cover` block on server.
 */
function register_block_core_cover() {
	register_block_type_from_metadata(
		__DIR__ . '/cover',
		array(
			'render_callback' => 'render_block_core_cover',
		)
	);
}
add_action( 'init', 'register_block_core_cover' );
