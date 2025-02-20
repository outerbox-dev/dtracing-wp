<?php
// Handle the CSV generation in batches
function generate_csv_event_handler() {
	error_log('CSV generation handler started.'); // Debugging
 // Ensure offset is non-negative
	$products = wc_get_products(array(
		'limit' => -1,
	));

	if (empty($products)) {
		update_option('generate_csv_offset', 0); // Reset offset when done
		update_option('generate_csv_progress', 100); // Mark progress as complete
		wp_clear_scheduled_hook('generate_csv_event'); // Clear the cron job
		error_log('CSV generation completed.'); // Debugging
		return;
	}
	$progress = 0;
	foreach ($products as $product) {
		$progress++;
		$sku = $product->get_sku();
		if (!$sku || $sku == '') {
			continue;
		}
		$price = $product->get_price();
		$cost = dtr_get_ordoro_price_by_sku($sku);
		$brand = '';
		$attributes = $product->get_attributes();

		if (isset($attributes['brand'])) {
			$brand = $attributes['brand']->get_options();
			$brand = is_array($brand) ? implode(', ', $brand) : $brand;
		}

		$margin = ($cost > 0) ? (($price - $cost) / $cost) * 100 : 0;

		$csv_data = array($sku, $brand, $price, $cost, number_format($margin, 2));
		append_to_csv($csv_data);
	}

	update_option('generate_csv_progress', $progress); // Update progress

	update_option('generate_csv_offset', $progress); // Update offset
	error_log('CSV generation batch completed. Progress: ' . $progress); // Debugging
}
add_action('generate_csv_event', 'generate_csv_event_handler');

// Function to append data to CSV
function append_to_csv($data) {
	$filename = WP_CONTENT_DIR . '/uploads/woocommerce_products_' . date('Y-m-d') . '.csv';

	$file = fopen($filename, 'a');

	if ($file) {
		fputcsv($file, $data);
		fclose($file);
	}
}

// AJAX handler to get CSV generation progress
function ajax_get_csv_generation_progress() {
	$progress = get_option('generate_csv_progress', 0);
	wp_send_json_success(array('progress' => $progress));
}
add_action('wp_ajax_get_csv_generation_progress', 'ajax_get_csv_generation_progress');

// Add a submenu page to trigger CSV generation
function add_generate_csv_link() {
	add_submenu_page(
		'tools.php',
		'Generate CSV',
		'Generate CSV',
		'manage_options',
		'generate-csv',
		'generate_csv_page_callback'
	);
}
add_action('admin_menu', 'add_generate_csv_link');

function generate_csv_page_callback() {
	?>
	<div class="wrap">
		<h1>Generate WooCommerce Products CSV</h1>
		<p><a href="<?php echo admin_url('admin-post.php?action=trigger_generate_csv'); ?>" class="button button-primary" id="generate-csv-button">Generate CSV</a></p>
		<div id="progress-container" style="display: none;">
			<p>Progress: <span id="csv-progress"></span></p>
			<progress id="csv-progress-bar" value="0" max="100"></progress>
		</div>
	</div>
	<script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#generate-csv-button').click(function(e) {
                e.preventDefault();
                $('#progress-container').show();
                checkProgress();
            });

            function checkProgress() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'get_csv_generation_progress'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#csv-progress').text(response.data.progress + ' Generated Products');
                            // $('#csv-progress-bar').val(response.data.progress);
                            if (response.data.progress < 100) {
                                setTimeout(checkProgress, 1000); // Check progress every second
                            }
                        }
                    }
                });
            }
        });
	</script>
	<?php
}

// Trigger the CSV generation process
function trigger_generate_csv() {
	error_log('CSV generation triggered.'); // Debugging
	update_option('generate_csv_progress', 0); // Reset progress
	update_option('generate_csv_offset', 0); // Reset offset

	if (!wp_next_scheduled('generate_csv_event')) {
		wp_schedule_single_event(time(), 'generate_csv_event');
	}

	wp_redirect(admin_url('tools.php?page=generate-csv'));
	exit;
}
add_action('admin_post_trigger_generate_csv', 'trigger_generate_csv');
