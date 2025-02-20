<?php
/**
 * The template part for displaying a popular products slider
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package QalaTheme\Templates\WooCommerce
 */

$should_hide = get_field( 'popular_products', 'options' );

// If this has been disabled for all archives, just bail.
if ( 'all' === $should_hide['hide_on_archive'] ) {
	return;
}

// If it's been disabled for some terms, let's check wether this is one of them.
if ( 'some' === $should_hide['hide_on_archive'] ) {
	$current_term = get_queried_object();
	if ( in_array( $current_term->term_taxonomy_id, $should_hide['hide_for_cat'], true ) ) {
		return;
	}
}

// Query popular products.
$products_query = new WP_Query(
	[
		'post_type'              => 'product',
		'posts_per_page'         => 16, // TODO: Might want some settings for this in the future.
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'post_status'            => 'publish',
		'ignore_sticky_posts'    => 1,
		'meta_key'               => 'total_sales',
		'orderby'                => 'meta_value_num',
	]
);

// Bail early if there's no popular products!
if ( ! $products_query->have_posts() ) {
	return;
}
?>
<div class="archive-popular-products slider-container">
	<div class="container">
		<header class="slider-header">
			<h2 class="h3 slider-header__title"><?php esc_html_e( 'Popular products', 'qala-theme' ); ?></h2>
			<?php get_template_part( 'template-parts/modules/partials/swiper-arrows' ); ?>
		</header>
		<div class="popular-products-slider">
			<?php
			global $product;
			woocommerce_product_loop_start();

			while ( $products_query->have_posts() ) :
				$products_query->the_post();
				/**
				 * WooCommerce core action
				 *
				 * Used before rendering the product card contents.
				 */
				do_action( 'woocommerce_shop_loop' );
				wc_get_template_part( 'content', 'product' );
			endwhile;

			woocommerce_product_loop_end();
			wp_reset_postdata();
			?>
		</div>
	</div>
</div>
