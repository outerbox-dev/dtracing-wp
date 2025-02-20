<?php
/**
 * Theme initialization
 *
 * @package qala
 */
add_filter( 'woocommerce_paypal_payments_simulate_cart_enabled', '__return_false' );
add_filter( 'woocommerce_paypal_payments_simulate_cart_prevent_updates', '__return_false' );

add_filter( 'dtracing_bootstrap_extra_paths', 'dtracing_add_extra_paths' );

/**
 * Add custom folders to the bootstrapping.
 * NOTE: Only needs to add the folder structure within /inc and the bootstrap will handle the rest.
 *
 * @param array $paths
 * @return array
 */
function dtracing_add_extra_paths( $paths ) {
	if ( class_exists( 'WooCommerce' ) ) {
		$paths[] = 'woocommerce';
	}
	return $paths;
}

// Load up the bootstrapping process.
require get_stylesheet_directory() . '/includes/classes/class-qala-bootstrap.php';
//require_once get_stylesheet_directory() . '/includes/generate_csv_with_margin_products.php';


function dtr_get_last_child_of_primary_category( $product_id ) {
	// Get the primary category ID set by Yoast SEO
	$primary_category_id = get_post_meta( $product_id, '_yoast_wpseo_primary_product_cat', true );

	if ( ! empty( $primary_category_id ) ) {

		$primary_category = get_term( $primary_category_id, 'product_cat' );

		return $primary_category->name;


		// Fetch all child categories of the primary category
		$child_categories = get_terms( array(
			'taxonomy'   => 'product_cat',
			'parent'     => $primary_category_id,
			'hide_empty' => false
		) );

		if ( ! empty( $child_categories ) ) {
			// Get the last child category
			$last_child_category = end( $child_categories );

			return $last_child_category->name;
		} else {
			// If no children, return the primary category itself
			$primary_category = get_term( $primary_category_id, 'product_cat' );

			return $primary_category->name;
		}
	}

	return 'N/A';
}

function update_product_on_prysync( $product_id ) {
	$product       = wc_get_product( $product_id );
	$category_name = dtr_get_last_child_of_primary_category( $product_id );
	$prisync_id    = get_post_meta( $product_id, '_prisync_id', true );
	if ( ! $prisync_id ) {
		create_product_on_prysync( $product_id );
		return;
	}
	$prisync_api_key   = 'info@dtracing.com';
	$prisync_api_token = '5b3f936d4cbae85e52fb5ffaed1bf4b0';

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, 'https://prisync.com/api/v2/edit/product/id/' . $prisync_id );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_POST, 1 );

	$cost = dtr_get_ordoro_price_by_sku( $product->get_sku() );
	if ( $product->is_type('variable') ) {
		$woo_price = $product->get_variation_price('max');
	} else {
		$woo_price = $product->get_price();
	}
	if ( ! $cost ) {
		$cost            = $woo_price;
		$additional_cost = 0.00;
	} else {
		$additional_cost = (float) $woo_price - (float) $cost;
	}

	$data = array(
		'name'            => $product->get_name(),
		'brand'           => $product->get_attribute( 'brand' ), // Make sure 'brand' is the correct attribute slug
		'category'        => $category_name,
		'cost'            => $cost,
		'additional_cost' => $additional_cost,
		'product_code'    => $product->get_sku(),
		'barcode'         => $product->get_sku(),
	);

	$headers = array(
		'apikey: ' . $prisync_api_key,
		'apitoken: ' . $prisync_api_token,
		'Content-Type: multipart/form-data'
	);

	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );

	$response = curl_exec( $ch );
	$err      = curl_error( $ch );

	curl_close( $ch );

	if ( $err ) {
		error_log( 'cURL Error #:' . $err );
	} else {
		error_log( 'Prysync update response: ' . $response );
	}
}

add_action( 'woocommerce_update_product', 'update_product_on_prysync' );

function create_product_on_prysync( $product_id ) {
	// Prisync API credentials
	$prisync_api_key   = 'info@dtracing.com';
	$prisync_api_token = '5b3f936d4cbae85e52fb5ffaed1bf4b0';
	$prisync_url       = 'https://prisync.com/api/v2/add/product/';


	$wc_product = wc_get_product( $product_id );
	$prisync_id = get_post_meta( $product_id, '_prisync_id', true );
	// Check if the product has the 'brand' attribute
	$brand = $wc_product->get_attribute( 'brand' );
	if ( empty( $brand ) || $prisync_id ) {
		return;
	}

	$cost = dtr_get_ordoro_price_by_sku( $wc_product->get_sku() );
	if ( $wc_product->is_type('variable') ) {
		$woo_price = $wc_product->get_variation_price('max');
	} else {
		$woo_price = $wc_product->get_price();
	}
	if ( ! $cost ) {
		$cost            = $woo_price;
		$additional_cost = 0.00;
	} else {
		$additional_cost = (float) $woo_price - (float) $cost;
	}

	// Prepare data for Prisync
	$data = array(
		'name'            => $wc_product->get_name(),
		'brand'           => $brand,
		'category'        => dtr_get_last_child_of_primary_category( $product->ID ) ?? 'N/A',
		'cost'            => $cost,
		'additional_cost' => $additional_cost,
		'product_code'    => $wc_product->get_sku(),
		'barcode'         => $wc_product->get_sku(),
	);

	$headers[] = 'apikey: ' . $prisync_api_key;
	$headers[] = 'apitoken: ' . $prisync_api_token;
	$headers[] = 'Content-Type: multipart/form-data';
	// Initialize cURL session for Prisync API calls
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

	// Send product to Prisync
	curl_setopt( $ch, CURLOPT_URL, $prisync_url );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );

	$response      = curl_exec( $ch );
	$response_data = json_decode( $response, true );

	// Save Prisync ID in WooCommerce product meta
	if ( ! empty( $response_data['id'] ) ) {
		update_post_meta( $wc_product->get_id(), '_prisync_id', $response_data['id'] );
	}

	$err      = curl_error( $ch );

	curl_close( $ch );

	if ( $err ) {
		error_log( 'cURL Error #:' . $err );
	} else {
		error_log( 'Prysync update response: ' . $response );
	}


}

add_action( 'woocommerce_new_product', 'create_product_on_prysync', 10, 1 );

function dtr_get_ordoro_price_by_sku( $product_sku ) {
	$api_credentials = 'i+prewkd3jXkttzyGNo8A2DUuczl65q+XKLPcJS6:/t2vf5IV+XtLfy9Yf723v16RleFQpgAUM8563uT9';


	// The endpoint URL for the product
	$url = "https://api.ordoro.com/product/$product_sku/";

	// Setup cURL
	$curl = curl_init();
	curl_setopt( $curl, CURLOPT_URL, $url );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Authorization: Basic ' . base64_encode( "$api_credentials" )
	) );

	// Execute the API request
	$response = curl_exec( $curl );

	// Check for errors in the cURL request
	if ( curl_errno( $curl ) ) {
		echo 'Curl error: ' . curl_error( $curl );
	}

	// Close the cURL session
	curl_close( $curl );

	// Decode the JSON response
	$data = json_decode( $response, true );

	return $data['price'] ?? false;

}


// Add custom column value in the order items table
function add_margin_column_value( $product, $item, $item_id ) {
	if ( $product ) {
		$quantity       = $item->get_quantity();
		$line_total     = $item->get_total();
		$purchase_price = dtr_get_ordoro_price_by_sku( $product->get_sku() );
		$item_margin    = $line_total - ( $purchase_price * $quantity );

		if ( $purchase_price > 0 ) {
			$item_margin_percentage = ( $item_margin / ( $line_total ) ) * 100;
		} else {
			$item_margin_percentage = 0;
		}

		return '<td class="margin_column">' . wc_price( $item_margin ) . ' (' . number_format( $item_margin_percentage, 2 ) . '%)</td>';
	}
}

// Add custom column style
add_action( 'admin_head', 'add_margin_column_style' );
function add_margin_column_style() {
	echo '<style>
        .margin_column {
            width: 15%;
        }
    </style>';
}

// Calculate and display total margin in the order totals table
function display_custom_margin_order_total( $order_id ) {
	$order        = wc_get_order( $order_id );
	$items        = $order->get_items();
	$total_margin = 0;

	foreach ( $items as $item ) {
		if ( $item->get_product_id() ) {
			$product_id     = $item->get_product_id();
			$product        = wc_get_product( $product_id );
			$quantity       = $item->get_quantity();
			$line_total     = $item->get_total(); // Use subtotal to exclude discounts
			$purchase_price = dtr_get_ordoro_price_by_sku( $product->get_sku() );
			if ( $purchase_price ) {
				$purchase_total = $purchase_price * $quantity;
				$item_margin    = $line_total - $purchase_total;
				$total_margin   += $item_margin;
			}
		}
	}

	if ( $total_margin > 0 ) {
		$margin_percentage = ( $total_margin / $order->get_subtotal() ) * 100;
	} else {
		$margin_percentage = 0;
	}
	?>
	<tr>
		<td class="label"><?php _e( 'Total Margin', 'woocommerce' ); ?>:</td>
		<td width="1%"></td>
		<td class="total">
            <span class="woocommerce-Price-amount amount">
                <?php echo wc_price( $total_margin ); ?> (<?php echo number_format( $margin_percentage, 2 ); ?>%)
            </span>
		</td>
	</tr>
	<?php
}

// AJAX callback function
add_action( 'wp_ajax_calculate_order_margin', 'calculate_order_margin_callback' );
function calculate_order_margin_callback() {
	if ( isset( $_POST['order_id'] ) ) {
		$order_id = intval( $_POST['order_id'] );

		// Capture the item margins output
		$order               = wc_get_order( $order_id );
		$items               = $order->get_items();
		$item_margin_columns = '';
		foreach ( $items as $item_id => $item ) {
			$product             = $item->get_product();
			$item_margin_columns .= add_margin_column_value( $product, $item, $item_id );
		}

		// Capture the total margin output
		ob_start();
		display_custom_margin_order_total( $order_id );
		$total_margin_html = ob_get_clean();

		wp_send_json_success( array(
			'item_margin_columns' => $item_margin_columns,
			'total_margin_html'   => '<div style="margin: 0;padding: 0;background: #fefefe;line-height: 1.4;text-align: center;font-size: 16px;">' . $total_margin_html . '</div>'
		) );
	} else {
		wp_send_json_error( 'Order ID not provided' );
	}
}


add_action( 'admin_enqueue_scripts', 'enqueue_custom_admin_script' );
function enqueue_custom_admin_script() {
	wp_enqueue_script( 'dtracing-custom-admin-script', get_template_directory_uri() . '/assets/js/custom-admin.js', array( 'jquery' ), '1.0.4', true );
	wp_localize_script( 'dtracing-custom-admin-script', 'custom_admin_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}



function auto_check_create_account( $checkout_fields ) {
    if ( ! is_user_logged_in() ) {
        // Set the create account checkbox to be checked by default
        $checkout_fields['account']['createaccount']['default'] = 1;
    }
    return $checkout_fields;
}
add_filter( 'woocommerce_checkout_fields', 'auto_check_create_account' );

function custom_hide_account_fields_css() {
    if ( is_checkout() && ! is_user_logged_in() ) {
        echo '<style>
            .woocommerce-account-fields {
                display: none !important;
            }
        </style>';
    }
}
add_action( 'wp_head', 'custom_hide_account_fields_css' );


// Remove admin ID meta on logout and clear cookie
add_action( 'wp_logout', 'remove_admin_user_id_meta' );

function remove_admin_user_id_meta() {
	$user_id = get_current_user_id();
	delete_user_meta( $user_id, '_admin_user_id' );

	// Clear the cookie by setting its expiration in the past
	if ( isset( $_COOKIE['admin_user_id'] ) ) {
		setcookie( 'admin_user_id', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN ); // Clear the cookie
	}
}

add_action('admin_footer', 'custom_woo_order_page_js');
function custom_woo_order_page_js() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.order_data_column:first-of-type .form-field:last-of-type').each(function() {
                if ($(this).text().trim().indexOf("Switch") !== -1) {
                    $(this).hide();
                }
            });
        });
    </script>
    <?php
}

// Function to add a "Choose user..." button or placeholder
function add_switch_link_placeholder( $order ) {
    ?>
    <div id="switch-to-customer-link" style="margin-top: 10px;">
        <p style="position:relative; top: 5px; left: 3px; color: #0073aa; cursor: pointer;" onclick="jQuery('#customer_user').select2('open');">
            <strong>Choose user...</strong>
        </p>
    </div>
    <?php
}
add_action( 'woocommerce_admin_order_data_after_order_details', 'add_switch_link_placeholder' );



function add_dynamic_link_updater_script() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            function updateSwitchToCustomerLink() {
                var customerId = $('#customer_user').val(); // This is the field where the customer is selected
                var orderId = $('input#post_ID').val(); // Order ID

                if (customerId) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'update_switch_link',
                            customer_id: customerId,
                            order_id: orderId,
                        },
                        success: function(response) {
                            $('#switch-to-customer-link').html(response);
                        }
                    });
                }
            }

            // Trigger the update function when the customer selection changes
            $('#customer_user').change(function() {
                updateSwitchToCustomerLink();
            });

            // Trigger the update on page load
            updateSwitchToCustomerLink();
        });
    </script>
    <?php
}
add_action( 'admin_footer', 'add_dynamic_link_updater_script' );

function update_switch_link_callback() {
    $customer_id = intval( $_POST['customer_id'] );
    $order_id = intval( $_POST['order_id'] );

    // Get the user object
    $user = get_user_by( 'id', $customer_id );

    if ( $user && current_user_can( 'administrator' ) ) {
        // Create the link to switch to the customer using the user ID directly
        $switch_link = add_query_arg( array(
            'user_id' => $user->ID, // Pass user ID to the URL
        ), admin_url() );

        echo '<p><a style="position:relative;top:5px;left: 3px;" href="' . esc_url( $switch_link ) . '">Switch User</a></p>';
    } else {
        echo '<p style="position:relative;top:5px;left: 3px;">No user associated with this order. (Guest Checkout or user not set)</p>';
    }

    wp_die(); // This is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_update_switch_link', 'update_switch_link_callback' );

function custom_login_as_user( $user_id ) {
    // Check if the current user can switch (must be admin or customer)
    if ( current_user_can( 'administrator' ) || current_user_can( 'customer' ) || current_user_can( 'subscriber' ) ) {
        $user = get_user_by( 'id', $user_id );

        if ( $user ) {
            // Save the current admin/customer ID in a cookie
            $admin_user_id = get_current_user_id();
            setcookie( 'admin_user_id', $admin_user_id, time() + 3600, COOKIEPATH, COOKIE_DOMAIN ); // Expires in 1 hour

            // Log in as the specified user (subscriber)
            wp_set_current_user( $user->ID );
            wp_set_auth_cookie( $user->ID );
            do_action( 'wp_login', $user->user_login, $user );
            wp_redirect( admin_url() ); // Redirect to the admin page
            exit;
        } else {
            wp_die( 'User not found.' );
        }
    } else {
        wp_die( 'Permission denied.' );
    }
}


// Endpoint to trigger the login as another user
function custom_login_as_user_endpoint() {
    if ( isset( $_GET['user_id'] ) && strlen( $_GET['user_id'] ) <= 4 && 
         strpos( $_SERVER['REQUEST_URI'], 'users.php' ) === false &&
         strpos( $_SERVER['REQUEST_URI'], 'user-edit.php' ) === false &&
         strpos( $_SERVER['REQUEST_URI'], 'admin.php?page=sales_channel' ) === false 
        ) 
    {
        custom_login_as_user( intval( $_GET['user_id'] ) ); // Switch to the provided user ID
    }
}
add_action( 'init', 'custom_login_as_user_endpoint' );

function custom_logout_to_admin() {
    // Check if the 'admin_user_id' cookie exists
    if ( isset( $_COOKIE['admin_user_id'] ) ) {
        $admin_user_id = $_COOKIE['admin_user_id'];
        
        // Log back in as the original admin or customer
        wp_set_current_user( $admin_user_id );
        wp_set_auth_cookie( $admin_user_id );
        do_action( 'wp_login', get_userdata( $admin_user_id )->user_login, get_userdata( $admin_user_id ) );
        
        // Clear the 'admin_user_id' cookie
        setcookie( 'admin_user_id', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN ); // Clear the cookie

        // Redirect back to the admin dashboard
        wp_redirect( admin_url() );
        exit;
    } else {
        wp_die( 'Session expired or invalid request.', 'Error' );
    }
}

if ( isset( $_GET['back_to_admin'] ) && $_GET['back_to_admin'] ) {
    custom_logout_to_admin();
}


function add_custom_meta_order( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( $order ) {
        // If the 'admin_user_id' cookie is set, use it. Otherwise, use the current user.
        if ( isset( $_COOKIE['admin_user_id'] ) ) {
            $admin_user_id = $_COOKIE['admin_user_id'];
            $order->update_meta_data( 'custom_user', $admin_user_id );
        } else {
            $logged_in_user_id = get_current_user_id();
            $order->update_meta_data( 'custom_user', $logged_in_user_id );
        }
        $order->save();
    }
}
add_action( 'woocommerce_thankyou', 'add_custom_meta_order' );



function replace_tilde_with_comma_in_query( $query ) {
    // Check if we're in the admin or if this is not the main query.
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    // Check if 'product_cat' parameter exists in the query.
    if ( isset( $query->query_vars['product_cat'] ) ) {
        // Replace tilde (~) with a comma.
        $query->query_vars['product_cat'] = str_replace( '~', ',', $query->query_vars['product_cat'] );
    }
}

// Hook the function into the 'pre_get_posts' action.
add_action( 'pre_get_posts', 'replace_tilde_with_comma_in_query' );


add_action( 'woocommerce_before_checkout_form', 'custom_order_payment_content', 10 );

function custom_order_payment_content( $checkout ) {
    // Check if we are on the pay for order page
    if ( isset( $_GET['pay_for_order'] ) && 'true' === $_GET['pay_for_order'] && isset( $_GET['key'] ) ) {
        global $wp;

        // Get the order ID from the URL
        if ( isset( $wp->query_vars['order-pay'] ) ) {
            $order_id = absint( $wp->query_vars['order-pay'] );

            // Get the order object
            $order = wc_get_order( $order_id );

            if ( $order ) {
                // Output only the total price
                echo '<div class="custom-order-payment-content">';
                echo '<h3 style="color:#000!important;margin-top:10px;margin-bottom:10px;">Total: '. wc_price( $order->get_total() ) . '</h3>';
                echo '</div>';
            } else {
                error_log( 'Order not found for ID: ' . $order_id );
            }
        } else {
            error_log( 'Order ID not found in URL.' );
        }
    }
}


// Add custom menu item
add_filter( 'woocommerce_account_menu_items', 'add_garage_account_tab', 99 );
function add_garage_account_tab( $items ) {
    // Insert the new tab after 'orders'
    $new_items = array_slice( $items, 0, array_search( 'orders', array_keys( $items ) ) + 1, true );
    $new_items['garage-tab'] = __( 'Garage', 'dtracing' );
    return array_merge( $new_items, array_slice( $items, array_search( 'orders', array_keys( $items ) ) + 1, null, true ) );
}

// Register endpoint for custom tab
add_action( 'init', 'add_garage_account_endpoint' );
function add_garage_account_endpoint() {
    add_rewrite_endpoint( 'garage-tab', EP_ROOT | EP_PAGES );
}




add_action('woocommerce_account_garage-tab_endpoint', 'garage_account_tab_content');
function garage_account_tab_content() {
    // Vehicle data
    $vehicles = [
        'Dodge' => [
            'Challenger' => [
                2008 => ['R/T', 'SRT 8'],
                2009 => ['R/T', 'SRT 8'],
                2010 => ['R/T', 'SRT 8'],
                2011 => ['R/T', 'SRT 8'],
                2012 => ['R/T', 'SRT 8'],
                2013 => ['R/T', 'SRT 8'],
                2014 => ['R/T', 'SRT 8'],
                2015 => ['R/T', 'Scat Pack', 'SRT 392', 'Hellcat'],
                2016 => ['R/T', 'Scat Pack', 'SRT 392', 'Hellcat'],
                2017 => ['R/T', 'Scat Pack', 'Hellcat'],
                2018 => ['R/T', 'Scat Pack', 'Hellcat', 'Demon'],
                2019 => ['R/T', 'Scat Pack', 'Hellcat', 'Redeye'],
                2020 => ['R/T', 'Scat Pack', 'Hellcat', 'Redeye'],
                2021 => ['R/T', 'Scat Pack', 'Hellcat', 'Redeye'],
                2022 => ['R/T', 'Scat Pack', 'Hellcat', 'Redeye', 'Super Stock'],
                2023 => ['Demon 170', 'R/T', 'Scat Pack', 'Hellcat', 'Redeye', 'Super Stock'],
            ],
            'Charger' => [
                2006 => ['R/T', 'SRT 8'],
                2007 => ['R/T', 'SRT 8'],
                2008 => ['R/T', 'SRT 8'],
                2009 => ['R/T', 'SRT 8'],
                2010 => ['R/T', 'SRT 8'],
                2011 => ['SRT 8', 'R/T'],
                2012 => ['SRT 8', 'R/T'],
                2013 => ['SRT 8', 'R/T'],
                2014 => ['SRT 8', 'R/T'],
                2015 => ['Scat Pack', 'SRT 392', 'Hellcat', 'R/T'],
                2016 => ['Scat Pack', 'SRT 392', 'Hellcat', 'R/T'],
                2017 => ['Scat Pack', 'Hellcat', 'R/T'],
                2018 => ['Scat Pack', 'Hellcat', 'R/T'],
                2019 => ['Scat Pack', 'Hellcat', 'R/T'],
                2020 => ['Scat Pack', 'Hellcat', 'R/T', 'Redeye'],
                2021 => ['Scat Pack', 'Hellcat', 'R/T', 'Redeye'],
                2022 => ['Scat Pack', 'Hellcat', 'R/T', 'Redeye'],
                2023 => ['Scat Pack', 'Hellcat', 'R/T', 'Redeye'],
            ],
            'Durango' => [
                2011 => ['R/T'],
                2012 => ['R/T'],
                2013 => ['R/T'],
                2014 => ['R/T'],
                2015 => ['R/T'],
                2016 => ['R/T'],
                2017 => ['R/T'],
                2018 => ['R/T', 'SRT 392'],
                2019 => ['R/T', 'SRT 392'],
                2020 => ['R/T', 'SRT 392'],
                2021 => ['Hellcat', 'R/T', 'SRT 392'],
                2022 => ['Hellcat', 'R/T', 'SRT 392'],
                2023 => ['Hellcat', 'R/T', 'SRT 392'],
            ],
            'Magnum' => [
                2005 => ['R/T'],
                2006 => ['R/T', 'SRT 8'],
                2007 => ['R/T', 'SRT 8'],
                2008 => ['R/T', 'SRT 8'],
            ],
        ],
        'Chrysler' => [
            '300' => [
                2006 => ['5.7 Hemi', 'SRT 8'],
                2007 => ['5.7 Hemi', 'SRT 8'],
                2008 => ['5.7 Hemi', 'SRT 8'],
                2009 => ['5.7 Hemi', 'SRT 8'],
                2010 => ['5.7 Hemi', 'SRT 8'],
                2011 => ['5.7 Hemi', 'SRT 8'],
                2012 => ['5.7 Hemi', 'SRT 8'],
                2013 => ['5.7 Hemi', 'SRT 8'],
                2014 => ['5.7 Hemi', 'SRT 8'],
                2015 => ['5.7 Hemi'],
                2016 => ['5.7 Hemi'],
                2017 => ['5.7 Hemi'],
                2018 => ['5.7 Hemi'],
                2019 => ['5.7 Hemi'],
                2020 => ['5.7 Hemi'],
                2021 => ['5.7 Hemi'],
                2022 => ['5.7 Hemi'],
                2023 => ['5.7 Hemi'],
            ],
        ],
        'Jeep' => [
            'Grand Cherokee' => [
                2006 => ['SRT 8'],
                2007 => ['SRT 8'],
                2008 => ['SRT 8'],
                2009 => ['SRT 8'],
                2010 => ['SRT 8'],
                2011 => ['SRT 8'],
                2012 => ['SRT 8'],
                2013 => ['SRT 8'],
                2014 => ['SRT'],
                2015 => ['SRT'],
                2016 => ['SRT'],
                2017 => ['SRT'],
                2018 => ['SRT', 'Trackhawk'],
                2019 => ['SRT', 'Trackhawk'],
                2020 => ['SRT', 'Trackhawk'],
                2021 => ['SRT', 'Trackhawk'],
                2022 => ['SRT'],
                2023 => ['SRT'],
            ],
            'Wrangler' => [
                2021 => ['Rubicon'],
                2022 => ['Rubicon'],
                2023 => ['Rubicon'],
            ],
        ],
        'Ram' => [
            '1500' => [
                2021 => ['TRX'],
                2022 => ['TRX'],
                2023 => ['TRX'],
                2024 => ['TRX'],
            ],
        ],
    ];

    // Output the form with select dropdowns
    ?>
    <div class="garage-page-vehicle-form js-garage-page-vehicle-form">
        <h3><?php esc_html_e('Select and Save Your Vehicle', 'dtracing'); ?></h3>
        <form style="display:flex;flex-direction:column;gap:15px;" class="garage-vehicle-form__form-container">
            <!-- Make Dropdown -->
            <div class="garage-vehicle-form__dropdown-container js-garage-vehicle-dropdown-container--make">
                <select style="border-radius: 3px;" class="garage-vehicle-form__dropdown garage-vehicle-form__dropdown--make js-garage-vehicle-dropdown">
                    <option disabled selected><?php esc_html_e('Select make', 'dtracing'); ?></option>
                    <?php foreach ($vehicles as $make => $models) : ?>
                        <option value="<?php echo esc_attr($make); ?>"><?php echo esc_html($make); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Model Dropdown (Initially Empty) -->
            <div class="garage-vehicle-form__dropdown-container js-garage-vehicle-dropdown-container--model">
                <select style="border-radius: 3px;" class="garage-vehicle-form__dropdown garage-vehicle-form__dropdown--model js-garage-vehicle-dropdown" disabled>
                    <option disabled selected><?php esc_html_e('Select model', 'dtracing'); ?></option>
                </select>
            </div>

            <!-- Year Dropdown (Initially Empty) -->
            <div class="garage-vehicle-form__dropdown-container js-garage-vehicle-dropdown-container--year">
                <select style="border-radius: 3px;" class="garage-vehicle-form__dropdown garage-vehicle-form__dropdown--year js-garage-vehicle-dropdown" disabled>
                    <option disabled selected><?php esc_html_e('Select year', 'dtracing'); ?></option>
                </select>
            </div>

            <!-- Trim Dropdown (Initially Empty) -->
            <div class="garage-vehicle-form__dropdown-container js-garage-vehicle-dropdown-container--trim">
                <select style="border-radius: 3px;" class="garage-vehicle-form__dropdown garage-vehicle-form__dropdown--trim js-garage-vehicle-dropdown" disabled>
                    <option disabled selected><?php esc_html_e('Select trim', 'dtracing'); ?></option>
                </select>
            </div>

            <button class="garage-vehicle-form__submit-button btn-large js-garage-vehicle-submit-button" type="submit">
                <?php esc_html_e('Save Vehicle', 'dtracing'); ?>
            </button>
        </form>
    </div>

	<!-- User's Saved Vehicles Section -->
    <div class="user-saved-vehicles">
        <h4 style="margin-top: 50px"><?php esc_html_e('Your Saved Vehicles', 'dtracing'); ?></h4>
        <div class="saved-vehicles-list js-saved-vehicles-list">
            <style>
                .saved-vehicles-list {
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                }
                .saved-vehicle-item {
                    background: white;
                    color: black;
                    display: flex;
                    justify-content: space-between;
                    padding: 10px;
                    border-radius: 3px;
                }
            </style>
        </div>
        <button id="clearVehiclesButton" style="cursor:pointer; color:red; display: none;">Clear All Vehicles</button>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const vehicles = <?php echo json_encode($vehicles); ?>;

            const makeSelect = document.querySelector('.garage-vehicle-form__dropdown--make');
            const modelSelect = document.querySelector('.garage-vehicle-form__dropdown--model');
            const yearSelect = document.querySelector('.garage-vehicle-form__dropdown--year');
            const trimSelect = document.querySelector('.garage-vehicle-form__dropdown--trim');

            // Event listener for Make selection
            makeSelect.addEventListener('change', function () {
                const selectedMake = makeSelect.value;

                // Clear the Model dropdown and reset Year and Trim dropdowns
                modelSelect.innerHTML = '<option disabled selected>Select model</option>';
                yearSelect.innerHTML = '<option disabled selected>Select year</option>';
                trimSelect.innerHTML = '<option disabled selected>Select trim</option>';

                // Populate the Model dropdown based on selected Make
                if (vehicles[selectedMake]) {
                    for (const model in vehicles[selectedMake]) {
                        const option = document.createElement('option');
                        option.value = model;
                        option.textContent = model;
                        modelSelect.appendChild(option);
                    }
                    modelSelect.disabled = false; // Enable the Model dropdown
                } else {
                    modelSelect.disabled = true;
                }
                
                yearSelect.disabled = true;
                trimSelect.disabled = true;
            });

            // Event listener for Model selection
            modelSelect.addEventListener('change', function () {
                const selectedMake = makeSelect.value;
                const selectedModel = modelSelect.value;

                // Clear the Year dropdown and reset Trim dropdown
                yearSelect.innerHTML = '<option disabled selected>Select year</option>';
                trimSelect.innerHTML = '<option disabled selected>Select trim</option>';

                // Populate the Year dropdown based on selected Make and Model
                if (vehicles[selectedMake] && vehicles[selectedMake][selectedModel]) {
                    for (const year in vehicles[selectedMake][selectedModel]) {
                        const option = document.createElement('option');
                        option.value = year;
                        option.textContent = year;
                        yearSelect.appendChild(option);
                    }
                    yearSelect.disabled = false; // Enable the Year dropdown
                } else {
                    yearSelect.disabled = true;
                }
                
                trimSelect.disabled = true;
            });

            // Event listener for Year selection
            yearSelect.addEventListener('change', function () {
                const selectedMake = makeSelect.value;
                const selectedModel = modelSelect.value;
                const selectedYear = yearSelect.value;

                // Clear the Trim dropdown
                trimSelect.innerHTML = '<option disabled selected>Select trim</option>';

                // Populate the Trim dropdown based on selected Make, Model, and Year
                if (vehicles[selectedMake] && vehicles[selectedMake][selectedModel] && vehicles[selectedMake][selectedModel][selectedYear]) {
                    vehicles[selectedMake][selectedModel][selectedYear].forEach(function (trim) {
                        const option = document.createElement('option');
                        option.value = trim;
                        option.textContent = trim;
                        trimSelect.appendChild(option);
                    });
                    trimSelect.disabled = false; // Enable the Trim dropdown
                } else {
                    trimSelect.disabled = true;
                }
            });
        });
    </script>

    <?php
}



function create_vehicle_selection_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_vehicle_data';

    // Check if the table already exists
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            make VARCHAR(255) NOT NULL,
            model VARCHAR(255) NOT NULL,
            year VARCHAR(255) NOT NULL,
            trim VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
add_action('init', 'create_vehicle_selection_table');



add_action('wp_ajax_save_user_vehicle_data', 'save_user_vehicle_data_ajax');
function save_user_vehicle_data_ajax() {
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in');
        return;
    }

    // Decode JSON data from POST request
    $vehicle_data = json_decode(file_get_contents('php://input'), true)['vehicle'];

    // Validate data
    if (!$vehicle_data || !isset($vehicle_data['make'], $vehicle_data['model'], $vehicle_data['year'], $vehicle_data['trim'])) {
        wp_send_json_error('Invalid data');
        return;
    }

    // Save data to the database
    save_user_vehicle_data(get_current_user_id(), $vehicle_data);
    wp_send_json_success('Vehicle data saved successfully');
}

// Helper function to save vehicle data to the database
function save_user_vehicle_data($user_id, $vehicle_data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_vehicle_data';

    // Insert new vehicle data without deleting previous entries
    $wpdb->insert($table_name, [
        'user_id' => $user_id,
        'make'    => $vehicle_data['make'],
        'model'   => $vehicle_data['model'],
        'year'    => $vehicle_data['year'],
        'trim'    => $vehicle_data['trim'],
    ]);
}


add_action('wp_ajax_get_user_vehicle_data', 'get_user_vehicle_data');
function get_user_vehicle_data() {
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in');
        return;
    }

    global $wpdb;
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . 'user_vehicle_data';

    // Retrieve user vehicle data
    $vehicle = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id), ARRAY_A);

    if ($vehicle) {
        wp_send_json_success(['vehicle' => $vehicle]);
    } else {
        wp_send_json_success(['vehicle' => null]);
    }
}

function enqueue_vehicle_script() {
    wp_enqueue_script('vehicle-script', get_stylesheet_directory_uri() . '/dist/vehicle-script.js', ['jquery'], null, true);

    // Localize script to provide the AJAX URL
    wp_localize_script('vehicle-script', 'vehicleData', [
        'ajaxurl' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_vehicle_script');

add_action('wp_ajax_delete_user_vehicle_data', 'delete_user_vehicle_data');
function delete_user_vehicle_data() {
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in', 400);
        return;
    }

    // Decode JSON data from the POST request
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate that vehicle_id is provided
    if (empty($data['vehicle_id'])) {
        wp_send_json_error('Invalid data', 400);
        return;
    }

    $vehicle_id = intval($data['vehicle_id']);

    global $wpdb;
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . 'user_vehicle_data';

    // Delete the vehicle entry from the database
    $deleted = $wpdb->delete($table_name, [
        'id' => $vehicle_id,
        'user_id' => $user_id,
    ]);

    if ($deleted) {
        wp_send_json_success('Vehicle deleted successfully');
    } else {
        wp_send_json_error('Error deleting vehicle', 400);
    }
}

add_action('wp_ajax_get_user_vehicle_data_modal', 'get_user_vehicle_data_modal');
function get_user_vehicle_data_modal() {
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in', 400);
        return;
    }

    global $wpdb;
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . 'user_vehicle_data';

    // Get the user's saved vehicles
    $vehicles = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id), ARRAY_A);

    if ($vehicles) {
        wp_send_json_success(['vehicle' => $vehicles]);
    } else {
        wp_send_json_success(['vehicle' => []]);
    }
}

add_action('wp_ajax_delete_user_vehicle_data_modal', 'delete_user_vehicle_data_modal');
function delete_user_vehicle_data_modal() {
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in', 400);
        return;
    }

    // Check if 'id' is set in the POST request
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        wp_send_json_error('Invalid data', 400);
        return;
    }

    $id = intval($_POST['id']);

    global $wpdb;
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . 'user_vehicle_data';

    // Delete the vehicle entry from the database
    $deleted = $wpdb->delete($table_name, [
        'id' => $id,
        'user_id' => $user_id,
    ]);

    if ($deleted) {
        wp_send_json_success('Vehicle deleted successfully');
    } else {
        wp_send_json_error('Error deleting vehicle', 400);
    }
}


function preload_featured_image_in_head() {    
    if (is_product()) {        
       global $post;        
       $featured_img_url = get_the_post_thumbnail_url($post->ID, 'full');                
 
       if ($featured_img_url) {            
          echo '<link rel="preload" href="' . esc_url($featured_img_url) . '" as="image" />';        
       } 
    }
 }
 
 add_action('wp_head', 'preload_featured_image_in_head');




 function inject_seo_boc() {    
    $current_term = get_queried_object();
    $seo_boc = get_field( 'seo_bottom_content', $current_term );

    if($seo_boc){
        echo '<div class="obx_seo_bottom_content_container">' . $seo_boc . '</div>';
    }
 }
 add_action('woocommerce_after_shop_loop', 'inject_seo_boc');

function inject_seo_toc() {    
    $current_term       = get_queried_object();
    $hero_seo_h1       = get_field( 'hero_custom_h1', $current_term );
    ?>
        <div class="cat-top-content">
            <?php if ($hero_seo_h1){ ?>
                <h1><?php echo esc_html( $hero_seo_h1 ); ?></h1>
            <?php }else{ ?>
                <h1><?php echo esc_html( $current_term->name ); ?></h1>
            <?php } ?>

            <div class="category-description">
                <?php echo category_description(); ?>
            </div>
        </div>

    <?php
}
add_action('woocommerce_before_shop_loop', 'inject_seo_toc');

add_action( 'woocommerce_after_checkout_billing_form', 'obx_checkout_text' );

function obx_checkout_text() {
    echo '<div class="new-checkout-text"><p>By checking this box and entering your phone number above, you consent to receive marketing text messages (such as promotion codes and cart reminders) from Dusterhoff Racing at the number provided, including messages sent by autodialer. Consent is not a condition of any purchase. Message and data rates may apply. Message frequency varies. You can unsubscribe at any time by replying STOP or clicking the unsubscribe link (where available) in one of our messages. View our <a target="_blank" href="https://dtracing.com/privacy-policy/">Privacy Policy</a> and <a target="_blank" href="https://dtracing.com/faq/terms-of-service/">Terms of Service</a>.</p></div>';
}
