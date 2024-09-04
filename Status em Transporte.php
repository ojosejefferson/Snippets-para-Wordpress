// Adiciona um novo status de pedido "Em transporte"
add_action( 'init', 'custom_register_shipping_status' );

function custom_register_shipping_status() {
    register_post_status( 'wc-shipping', array(
        'label'                     => _x( 'Em transporte', 'WooCommerce Order status', 'woocommerce' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Em transporte 🚚 <span class="count" style="background-color:#007D8C;">(%s)</span>', 'Em transporte 🚚 <span class="count" style="background-color:#007D8C;">(%s)</span>', 'woocommerce' )
    ) );
}

// Adiciona o novo status de pedido ao dropdown de status do administrador
add_filter( 'wc_order_statuses', 'custom_add_shipping_order_status' );

function custom_add_shipping_order_status( $order_statuses ) {
    $order_statuses['wc-shipping'] = _x( 'Em transporte', 'WooCommerce Order status', 'woocommerce' );
    return $order_statuses;
}
// Adicionar notificação de e-mail quando o status do pedido for alterado para "Em transporte"
add_action( 'woocommerce_order_status_changed', 'custom_send_shipping_email_notification', 10, 4 );

function custom_send_shipping_email_notification( $order_id, $old_status, $new_status, $order ) {
    if ( $new_status === 'wc-shipping' ) {
        $email_subject = __('Seu pedido está em transporte', 'text-domain');
        $email_content = __('Seu pedido está em transporte e a caminho. Obrigado por comprar conosco!', 'text-domain');

        // Envie o e-mail de notificação para o cliente
        wc_mail( $order->get_billing_email(), $email_subject, $email_content );
    }
}

// Adicionar opção "Em transporte" ao dropdown de ações em massa
add_filter( 'bulk_actions-edit-shop_order', 'custom_add_shipping_bulk_action' );

function custom_add_shipping_bulk_action( $bulk_actions ) {
    // Adiciona a ação "Em transporte" ao dropdown
    $bulk_actions['mark_shipping'] = __( 'Marcar como Em transporte', 'text-domain' );
    return $bulk_actions;
}

// Processar ação em massa quando "Em transporte" for selecionado
add_filter( 'handle_bulk_actions-edit-shop_order', 'custom_handle_shipping_bulk_action', 10, 3 );

function custom_handle_shipping_bulk_action( $redirect_to, $action, $post_ids ) {
    if ( $action === 'mark_shipping' ) {
        foreach ( $post_ids as $post_id ) {
            // Altere o status do pedido para "Em transporte"
            $order = wc_get_order( $post_id );
            if ( $order ) {
                $order->update_status( 'wc-shipping' );
            }
        }
        // Redirecionar de volta para a página de pedidos
        $redirect_to = add_query_arg( 'bulk_shipping_success', count( $post_ids ), $redirect_to );
    }
    return $redirect_to;
}

add_action( 'admin_enqueue_scripts', 'custom_admin_styles' );
function custom_admin_styles() {
    // Verifica se é a página de edição de pedidos do WooCommerce
    if ( isset( $_GET['page'] ) && $_GET['page'] === 'wc-orders' ) {
        // Adiciona o CSS personalizado para colorir o status "Em transporte"
        $custom_css = "
        .order-status[data-order-status='wc-shipping'] .status-name {
            background-color: #007D8C; /* Cor personalizada */
        }";
        wp_add_inline_style( 'woocommerce_admin_styles', $custom_css );
    }
}
