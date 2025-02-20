jQuery(document).ready(function($) {
    if ($('#post_type').val() === 'shop_order') {
        $.ajax({
            url: custom_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'calculate_order_margin',
                order_id: $('#post_ID').val()
            },
            success: function(response) {
                console.log(response);
                if (response.success) {
                    $('#woocommerce-order-items').append(response.data.total_margin_html);
                    $('.woocommerce_order_items_wrapper table.wc-order-items').append(response.data.item_margin_columns);
                }
            }
        });
    }
});
