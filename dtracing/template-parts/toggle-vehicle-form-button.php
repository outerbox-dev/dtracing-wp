<?php
	/**
	 * Template for the toggle vehicle form button form.
	 */

?>
<button class="toggle-vehicle-form-button js-toggle-vehicle-form-button" aria-label="<?php esc_html_e( 'Open vehicle select form', 'qala-theme' ); ?>">
	<?php ac_svg( 'car', true, 'icons' ); ?>
	<span class="toggle-vehicle-form-button__count js-toggle-vehicle-form-button-count is-hidden">1</span>
</button>
