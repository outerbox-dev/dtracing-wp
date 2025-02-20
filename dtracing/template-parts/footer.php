<?php
/**
 * Regular footer content.
 *
 * @package QalaTheme\Templates
 */

$footer_settings            = get_field( 'footer_settings', 'option' );
$footer_icon_id             = $footer_settings['footer_logotype'];
$footer_text_about_company  = $footer_settings['footer_short_about_text'];
$footer_newsletter_heading  = $footer_settings['footer_newsletter_heading'];
$footer_newsletter          = $footer_settings['footer_newsletter'];
$footer_show_contact        = $footer_settings['footer_show_contact'];
$footer_show_menu           = $footer_settings['footer_show_menu'];
$footer_show_second_menu    = $footer_settings['show_secondary_footer_menu'];
$footer_secondary_links     = $footer_settings['secondary_footer_menu'];
$footer_show_contact_icons  = $footer_settings['footer_show_contact_icons'];
$footer_contact_header      = '';
$footer_adress              = '';
$footer_phone_number        = '';
$footer_email               = '';
$footer_menu_heading        = '';
$footer_second_menu_heading = '';
$footer_menu_class          = '';

$footer_show_payment_methods = $footer_settings['footer_show_payment_methods'];

if ( $footer_show_menu ) {
	$footer_menu_heading = $footer_settings['footer_menu_heading'];
}

if ( $footer_show_second_menu ) {
	$footer_second_menu_heading = $footer_settings['secondary_footer_menu_title'];
}

if ( $footer_show_menu && $footer_show_second_menu ) {
	$footer_menu_class = ' footer-column--menus-2';
}

if ( $footer_show_contact ) {
	$footer_contact_header = $footer_settings['footer_contact_heading'];
	$footer_adress         = get_field( 'contact_adress', 'option' );
	$footer_phone_number   = get_field( 'contact_phone_number', 'option' );
	$footer_email          = get_field( 'contact_email', 'option' );
}

$privacy_page_link = get_privacy_policy_url();
$terms_page_link   = $footer_settings['terms_conditions_link'];

?>

<?php
/**
 * Before footer content.
 */
do_action( 'qala_theme/action/before/footer_content' );
?>

<div class="container footer-top">
	<div class="grid footer-content" role="contentinfo">
		<div class="col-m-4 footer-column--contact">
			<?php if ( $footer_icon_id ) : ?>
				<a class="footer-logo" href="<?php echo esc_html( site_url() ); ?>" title="<?php bloginfo( 'name' ); ?>">
					<?php echo wp_get_attachment_image( $footer_icon_id, 'footer_logo' ); ?>
				</a>
			<?php endif; ?>
			<?php if ( ! empty( $footer_text_about_company ) ) : ?>
				<div class="footer-about-text"><?php echo $footer_text_about_company; //phpcs:ignore ?></div>
			<?php endif; ?>
			<?php if ( $footer_show_contact ) : ?>
				<div class="footer-contact<?php echo ( ! $footer_show_contact_icons ) ? esc_attr( ' hide-icons' ) : ''; ?>">
					<?php if ( $footer_contact_header ) : ?>
						<div class="footer-heading"><?php echo esc_html( $footer_contact_header ); ?></div>
					<?php endif; ?>
					<p>
						<?php if ( $footer_adress ) : ?>
							<span><?php echo esc_html( ac_svg( 'place', true, 'icons/social' ) ); ?><?php echo $footer_adress; //phpcs:ignore ?></span>
						<?php endif; ?>
						<?php if ( $footer_phone_number ) : ?>
							<span><a href="tel:<?php echo esc_html( preg_replace( '/\s/', '', $footer_phone_number ) ); ?>"><?php echo esc_html( ac_svg( 'phone', true, 'icons/social' ) ); ?><?php echo esc_html( $footer_phone_number ); ?></a></span>
						<?php endif; ?>
						<?php if ( $footer_email ) : ?>
							<span><a href="mailto:<?php echo esc_html( $footer_email ); ?>"><?php echo esc_html( ac_svg( 'mail', true, 'icons/social' ) ); ?><?php echo esc_html( $footer_email ); ?></a></span>
						<?php endif; ?>
					</p>
				</div>
				<?php
				/**
				 * After footer content
				 */
				do_action( 'qala_theme/action/after/footer_content' );
				?>
			<?php endif; ?>
		</div>
		<div class="col-m-4 footer-column--menus<?php echo esc_html( $footer_menu_class ); ?>">
			<div class="col-m-6">
				<?php if ( $footer_show_menu ) : ?>
					<?php if ( $footer_menu_heading ) : ?>
						<div class="footer-heading footer-menu-heading"><?php echo esc_html( $footer_menu_heading ); ?></div>
					<?php endif; ?>
					<?php
					wp_nav_menu(
						[
							'theme_location'  => 'footer',
							'menu_class'      => 'footer-menu',
							'container'       => 'nav',
							'container_class' => 'footer-menu-wrapper',
							'fallback_cb'     => false,
						]
					);
					?>
				<?php endif; ?>
			</div>
			<?php if ( $footer_show_second_menu ) : ?>
				<div class="col-m-6 footer-menu-wrapper">
					<?php if ( $footer_second_menu_heading ) : ?>
						<div class="footer-heading footer-menu-heading"><?php echo esc_html( $footer_second_menu_heading ); ?></div>
					<?php endif; ?>
					<?php
					if ( count( $footer_secondary_links ) > 0 ) :
						?>
						<ul class="footer-menu">
							<?php
							foreach ( $footer_secondary_links as $secondary_link ) : the_row(); // phpcs:ignore
								$single_row_link = isset( $secondary_link['footer_secondary_menu_item'] ) ? $secondary_link['footer_secondary_menu_item'] : null;

								if ( ! $single_row_link || ! is_array( $single_row_link ) ) {
									continue;
								}

								$link_target = $single_row_link['target'] ? $single_row_link['target'] : '_self';
								?>
								<li class="menu-item">
									<a href="<?php echo esc_url( $single_row_link['url'] ); ?>" target="<?php echo esc_attr( $link_target ); ?>">
										<?php echo esc_html( $single_row_link['title'] ); ?>
									</a>
								</li>
								<?php
							endforeach;
							?>
						</ul>
						<?php
					endif;
					?>
				</div>
			<?php endif; ?>
		</div>
		<div class="col-m-4 footer-column--newsletter">
			<div class="footer-newsletter footer-newsletter--fancy-labels">
				<div class="klaviyo-form-SbRBA2"></div>
				<hr>
				<div class="form-disclaimer">
					<p>By submitting this form, you are consenting to receive marketing emails from: Dusterhoff Racing. You can revoke your consent to receive emails at any time by using the unsubscribe link found at the bottom of every email. </p>
				</div>
				<div style="display: none;">
					<?php if ( $footer_newsletter_heading ) : ?>
						<div class="footer-heading">
							<?php echo esc_html( $footer_newsletter_heading ); ?>
						</div>
					<?php endif; ?>
					<?php if ( $footer_newsletter ) : ?>
						<?php echo do_shortcode( $footer_newsletter ); ?>
					<?php endif; ?>
				</div>
				
			</div>
		</div>
	</div>
</div>

<div class="footer-copyright" id="sub-footer">
	<div class="container">
		<div class="footer-bottom-wrapper">
			<div class="footer-bottom-column column--country-selector">
			<?php
			if ( is_multisite() ) {
				do_action(
					'qala_multimarket/render_country_selector',
					[
						'classes'    => 'footer-country-selector',
						'show_globe' => true,
						'content'    => '',
					]
				);
			}
			?>
			</div>
			<div class="footer-bottom-column column--info">
				<div class="info--legal-links">
					
					<?php
					if ( $privacy_page_link ) {
						?>
						<a title="Policy" href="<?php echo esc_url( $privacy_page_link ); ?>"><?php echo esc_html_e( 'Privacy Policy', 'qala-theme' ); ?></a>
						<?php
					}
					?>
				</div>
				<div class="info--copyrights">
					<p class="site-info">
						<?php /* translators: translate the text copyright. */ ?>
						<span id="special">©</span> <?php printf( __( 'Copyright %1$s %2$s all rights reserved', 'qala-theme' ), date( 'Y' ), get_bloginfo( 'name' ) ); // phpcs:ignore ?>
					</p>
				</div>
			</div>
			<div class="footer-bottom-column column--payment-methods">
				<?php
				if ( $footer_show_payment_methods && is_woocommerce_activated() && ( have_rows( 'payment_gateway_selections', 'option' ) || have_rows( 'payment_gateway_items', 'option' ) ) ) {
					?>
					<div class="footer-payment-gateways">
						<?php get_template_part( 'template-parts/partials/payment-methods-list' ); ?>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
