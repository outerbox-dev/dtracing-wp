<?php

/* Template Name: Download Sales Orders */

if ( isset( $_POST['download_csv'] ) ) {
	$start_date = sanitize_text_field( $_POST['start_date'] );
	$end_date   = sanitize_text_field( $_POST['end_date'] );

	// WooCommerce order query
	$args = array(
		'post_type'      => 'shop_order',
		'post_status'    => array_keys( wc_get_order_statuses() ),
		'date_query'     => array(
			'after'     => $start_date,
			'before'    => $end_date,
			'inclusive' => true,
		),
		'meta_query'     => array(
			array(
				'key'     => 'custom_user',
				'compare' => 'EXISTS',
			),
		),
		'posts_per_page' => - 1,
	);

	$orders = get_posts( $args );

	if ( $orders ) {
		// Prepare the CSV headers
		$csv_data   = array();
        $csv_data[] = array('Order ID', 'Custom User ID', 'Sales Email', 'First Name', 'Last Name', 'Order Total', 'Order Status', 'Order Items');

		// Loop through orders and extract required data
		foreach ( $orders as $order_post ) {
			$order          = wc_get_order( $order_post->ID );
			$custom_user_id = get_post_meta( $order->get_id(), 'custom_user', true );

			if ( $custom_user_id ) {
				$user            = get_user_by( 'id', $custom_user_id );
				if ( $user && in_array( 'administrator', $user->roles ) ) {
					$user_email      = $user ? $user->user_email : '';
					$user_first_name = $user ? $user->first_name : '';
					$user_last_name  = $user ? $user->last_name : '';
					$order_total     = $order->get_total();
					$order_status = wc_get_order_status_name($order->get_status());

					// Get order items and concatenate product names
					$items = $order->get_items();
					$product_names = array();
					foreach ($items as $item) {
						$product_names[] = $item->get_name();
					}
					$order_items = implode(' | ', $product_names);

					$csv_data[] = array(
						$order->get_id(),
						$custom_user_id,
						$user_email,
						$user_first_name,
						$user_last_name,
						$order_total,
						$order_status,
						$order_items,
					);
				}
			}
		}

		// Generate CSV
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=orders.csv' );
		$output = fopen( 'php://output', 'w' );
		foreach ( $csv_data as $row ) {
			fputcsv( $output, $row );
		}
		fclose( $output );
		exit;
	} else {
		echo '<p>No orders found for the selected dates or no orders with the custom_user meta field.</p>';
	}
}

?>

<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='UTF-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1.0'>
	<title>Download Orders</title>
	<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin: 10px 0 5px;
            font-weight: bold;
            color: #555;
        }

        input[type='date'] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            width: 100%;
            max-width: 300px;
            box-sizing: border-box;
        }

        button {
            background-color: #1232da;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #215388;
        }
	</style>
</head>
<body>
<div class='form-container'>
	<h1>Download Sales Orders</h1>
	<form method='post'>
		<label for='start_date'>Start Date:</label> <input type='date' name='start_date' required>

		<label for='end_date'>End Date:</label> <input type='date' name='end_date' required>

		<button type='submit' name='download_csv'>Download CSV</button>
	</form>
</div>
</body>
</html>
