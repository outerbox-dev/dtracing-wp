<?php
/**
 * Search results template.
 */

get_header();
global $wp_query;

$is_elasticsearch_used          = true;
$search_filter_configuration_id = get_field( 'which_filter_configuration_to_use_on_search_page', 'options' );

?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">
		<?php if ( $is_elasticsearch_used ) : ?>
			<div class="container">
				<?php echo do_shortcode( sprintf( '[qala_elastic_filters_render filter_config="%d" object_type="product"]', $search_filter_configuration_id ) ); ?>
			</div>
		<?php endif; ?>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
