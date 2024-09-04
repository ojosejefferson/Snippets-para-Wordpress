add_filter( 'wc_order_statuses', 'custom_change_order_statuses' );

function custom_change_order_statuses( $order_statuses ) {
    $order_statuses['wc-processing'] = _x( 'Aprovado', 'Order status', 'woocommerce' );

    return $order_statuses;
}