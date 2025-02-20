<?php
	/**
	 * Template for the select vehicle form.
	 */

$is_make_dropdown_disabled  = true;
$is_model_dropdown_disabled = true;
$is_year_dropdown_disabled  = true;
$is_trim_dropdown_disabled  = true;

?>

<div class="select-vehicle-form js-select-vehicle-form is-hidden-on-small-screens">
	<div class="container select-vehicle-form__container" <?php if (is_shop() || is_search()) : ?> style="max-height:66px" <?php endif; ?>>
		<?php $shop_page = is_shop() || is_search() ? 'shop' : ''; ?>
		<div class="h6 select-vehicle-form__title" style="<?php if (is_shop() || is_search()) {?>min-width: fit-content;<?php }; ?>">
			<span class="select-vehicle-form__saved-cars">0</span>
			<?php esc_html_e( 'Select your vehicle', 'dtracing' ); ?>
		</div>
		
		<form class="select-vehicle-form__form-container <?php echo esc_attr($shop_page); ?>" style="<?php if (is_shop() || is_search()) {?>margin-right:0;<?php }; ?>">
			<div class="select-vehicle-form__dropdown-container js-select-vehicle-dropdown-container--make <?php echo $is_make_dropdown_disabled ? 'select-vehicle-form__dropdown-container--disabled' : ''; ?>">
				<select class="select-vehicle-form__dropdown select-vehicle-form__dropdown--make js-select-vehicle-dropdown" required <?php disabled( $is_make_dropdown_disabled ); ?>>
					<option disabled selected><?php esc_html_e( 'Select make', 'dtracing' ); ?></option>
				</select>
				<span class="select-vehicle-form__dropdown-counter">1</span>
			</div>

			<div class="select-vehicle-form__dropdown-container js-select-vehicle-dropdown-container--model <?php echo $is_model_dropdown_disabled ? 'select-vehicle-form__dropdown-container--disabled' : ''; ?>">
				<select class="select-vehicle-form__dropdown select-vehicle-form__dropdown--model js-select-vehicle-dropdown" <?php disabled( $is_model_dropdown_disabled ); ?> required>
					<option disabled selected><?php esc_html_e( 'Select model', 'dtracing' ); ?></option>
				</select>

				<span class="select-vehicle-form__dropdown-counter">2</span>
			</div>

			<div class="select-vehicle-form__dropdown-container js-select-vehicle-dropdown-container--year <?php echo $is_year_dropdown_disabled ? 'select-vehicle-form__dropdown-container--disabled' : ''; ?>">
				<select class="select-vehicle-form__dropdown select-vehicle-form__dropdown--year js-select-vehicle-dropdown" <?php disabled( $is_year_dropdown_disabled ); ?> required>
					<option disabled selected><?php esc_html_e( 'Select year', 'dtracing' ); ?></option>
				</select>

				<span class="select-vehicle-form__dropdown-counter">3</span>
			</div>

			<div class="select-vehicle-form__dropdown-container js-select-vehicle-dropdown-container--trim <?php echo $is_trim_dropdown_disabled ? 'select-vehicle-form__dropdown-container--disabled' : ''; ?>">
				<select class="select-vehicle-form__dropdown select-vehicle-form__dropdown--trim js-select-vehicle-dropdown" <?php disabled( $is_trim_dropdown_disabled ); ?> required>
					<option disabled selected><?php esc_html_e( 'Select trim', 'dtracing' ); ?></option>
				</select>

				<span class="select-vehicle-form__dropdown-counter">4</span>
			</div>
			<?php if (is_shop() || is_search()) : ?>
				<button class="select-vehicle-form__submit-button btn-large js-select-vehicle-submit-button ">Search</button>
			<?php endif; ?>
		</form>
		<form class="select-vehicle-form__search-form <?php echo esc_attr($shop_page); ?>" method="get" style="<?php if (is_shop() || is_search()) {?>margin-right: 150px;<?php }; ?>">
			<input type="search" name="s" placeholder="<?php esc_attr_e( 'Search', 'dtracing' ); ?>" style="<?php if (is_shop() || is_search()) {?>width:-webkit-fill-available;<?php }; ?>visibility:hidden">

			<input type="hidden" name="product_cat" value="" />

			<input style="visibility:hidden;" type="submit" value="<?php esc_html_e( 'Search', 'dtracing' ); ?>">
		</form>


	</div>
	<button class="select-vehicle-form__submit-button btn-large js-select-vehicle-submit-button <?php echo esc_attr($shop_page); ?>" type="submit"><?php esc_html_e( 'Search', 'dtracing' ); ?></button>
</div>
<div class="select-vehicle-form__popup hide">
	<div class="select-vehicle-form__popup-overlay"></div>
	<div class="select-vehicle-form__popup-wrapper">
		<div class="select-vehicle-form__popup-content">
			<div class="select-vehicle-form__popup-empty hide">
				No saved cars
			</div>

			<div class="select-vehicle-form__popup-active hide">
				Active Car

			</div>
			<div class="select-vehicle-form__popup-saved hide">
				Saved Cars
			</div>
		</div>
	</div>
</div>