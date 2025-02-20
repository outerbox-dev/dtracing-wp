<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package QalaTheme
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<meta name="google-site-verification" content="gMXaId8rOqWlqZyXCyJc6okrBHzJaUrU-j9IFO733X4" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="hfeed site">

	<?php
		/**
		* Show the information-bar above the header and above usp-bar
		*/
		get_template_part(
			'template-parts/information-bar-position',
			null,
			[
				'header_position' => 'above',
				'usp_position'    => 'above',
			]
		);
		?>

	<?php
	/**
	 * Show the USP-bar above the header.
	 */
	get_template_part( '/template-parts/usp-bar', null, [ 'position' => 'above' ] );
	?>

	<?php
		/**
		* Show the information-bar above the header and below usp-bar
		*/
		get_template_part(
			'template-parts/information-bar-position',
			null,
			[
				'header_position' => 'above',
				'usp_position'    => 'below',
			]
		);
		?>

	<?php
		/**
		 * Check if we are using layout_logo_centered
		 *
		 */
		$header_toggles = get_field( 'top_header', 'options' );
	?>

	<header id="masthead" class="site-header <?php echo $header_toggles['layout_logo_centered'] ? 'site-header--layout-logo-centered' : ''; ?>">

		<div class="container">
			<nav id="site-navigation" class="main-navigation">
				<?php if ( $header_toggles['layout_logo_centered'] ) : ?>
					<div class="site-search-form <?php echo $header_toggles['disable_layout_logo_centered_mobile_search'] ? 'site-search-form--hide-mob' : ''; ?>">
						<?php get_search_form(); ?>
					</div>
				<?php endif; ?>
				<?php qala_site_branding(); ?>
				<a class="skip-link screen-reader-text sr-only" href="#content">
					<?php esc_html_e( 'Skip to content', 'qala-theme' ); ?>
				</a>
				<?php
				/**
				 * Show a top header.
				 */
				get_template_part( '/template-parts/main-navigation' );

				/**
				 * Show the button for toggling select vehicle form
				 */
				get_template_part( '/template-parts/toggle-vehicle-form-button' );

				/**
				 * Show right column.
				 */
				get_template_part( '/template-parts/header-right-column' );

				/**
				 * Show Buttons.
				 */
				get_template_part( '/template-parts/header-buttons' );

				?>
			</nav>
		</div>
		<?php if ( ! $header_toggles['layout_logo_centered'] || ( $header_toggles['layout_logo_centered'] && $header_toggles['disable_layout_logo_centered_mobile_search'] ) ) : ?>
			<?php get_template_part( 'template-parts/header', 'search' ); ?>
		<?php endif; ?>

		<?php
		get_template_part( '/template-parts/select-vehicle-form' );
		?>
	</header>

	<?php
		/**
		* Show the information-bar below the header and above usp-bar
		*/
		get_template_part(
			'template-parts/information-bar-position',
			null,
			[
				'header_position' => 'below',
				'usp_position'    => 'above',
			]
		);
		?>

	<?php
	/**
	 * Show the USP-bar below the header.
	 */
	get_template_part( '/template-parts/usp-bar', null, [ 'position' => 'below' ] );
	?>

	<?php
		/**
		* Show the information-bar below the header and below usp-bar
		*/
		get_template_part(
			'template-parts/information-bar-position',
			null,
			[
				'header_position' => 'below',
				'usp_position'    => 'below',
			]
		);
		?>

	<div id="content" class="site-content">
