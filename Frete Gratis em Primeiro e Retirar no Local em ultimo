add_filter( 'woocommerce_package_rates', 'custom_sort_shipping_methods', 9999, 2 );

function custom_sort_shipping_methods( $rates, $package ) {
    // Separar os métodos de envio em duas categorias: fretes normais e retirada no local
    $free_shipping = array();
    $regular_shipping = array();
    $local_pickup = array();
    
    foreach ( $rates as $rate_key => $rate ) {
        if ( 'free_shipping' === $rate->method_id ) {
            $free_shipping[ $rate_key ] = $rate;
        } elseif ( 'local_pickup' === $rate->method_id ) {
            $local_pickup[ $rate_key ] = $rate;
        } else {
            $regular_shipping[ $rate_key ] = $rate;
        }
    }
    
    // Se houver frete grátis, colocá-lo na frente, seguido dos fretes normais e retirada no local
    if ( !empty( $free_shipping ) ) {
        $sorted_rates = $free_shipping + $regular_shipping + $local_pickup;
    } else {
        // Se não houver frete grátis, colocar os fretes normais na frente e retirada no local por último
        $sorted_rates = $regular_shipping + $local_pickup;
    }
    
    return $sorted_rates;
}
