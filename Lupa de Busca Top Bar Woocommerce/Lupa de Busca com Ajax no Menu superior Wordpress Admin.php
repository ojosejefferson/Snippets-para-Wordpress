// Adicionar o ícone de busca ao admin bar
add_action('admin_bar_menu', 'add_wc_product_search_icon', 999);
function add_wc_product_search_icon($wp_admin_bar) {
    $args = array(
        'id'    => 'wc_product_search',
        'title' => '<span class="ab-icon dashicons dashicons-search"></span>',
        'href'  => '#',
        'meta'  => array(
            'class' => 'wc-product-search',
        ),
    );
    $wp_admin_bar->add_node($args);
}

// Adicionar a barra de pesquisa no footer da área administrativa e no front-end
function add_wc_product_search_bar() {
    if (!is_user_logged_in() || !current_user_can('manage_woocommerce')) {
        return; // Somente administradores logados podem ver a barra de pesquisa
    }

    ?>
    <div id="wc-product-search-container" style="display:none;">
        <input type="text" id="wc-product-search-input" placeholder="Digite o nome do produto..." />
        <div id="wc-product-search-results"></div>
    </div>
    <style>
        #wc-product-search-container {
            position: absolute;
            top: 32px;
			left: 40%;            
			background: #fff;
            border: 1px solid #ddd;
            z-index: 10000;
            padding: 10px;
			border-radius: 7px;
			transform: translateX(-50%)
        }
        #wc-product-search-input {
            width: 300px;
            padding: 5px;
        }
        #wc-product-search-results {
            margin-top: 10px;
        }
        #wc-product-search-results a {
            display: block;
            padding: 5px;
            text-decoration: none;
            color: #0073aa;
        }
        #wc-product-search-results a:hover {
            background: #f1f1f1;
        }
    </style>
    <script type="text/javascript">
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>"; // Define ajaxurl no front-end

        jQuery(document).ready(function($) {
            $('#wp-admin-bar-wc_product_search').on('click', function(e) {
                e.preventDefault();
                $('#wc-product-search-container').toggle();
                $('#wc-product-search-input').focus();
            });
        // Fechar a barra de pesquisa ao pressionar Esc
        $(document).on('keydown', function(e) {
            if (e.key === "Escape") {
                $('#wc-product-search-container').hide();
            }
        });		
			        // Fechar a barra de pesquisa ao clicar fora dela
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#wc-product-search-container, #wp-admin-bar-wc_product_search').length) {
                $('#wc-product-search-container').hide();
            }
        });

            $('#wc-product-search-input').on('keyup', function() {
                var query = $(this).val();

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        'action': 'search_wc_products',
                        'query': query
                    },
                    success: function(response) {
                        $('#wc-product-search-results').html(response);
                    }
                });
            });
        });
    </script>
    <?php
}

// Adicionar a barra de pesquisa ao admin_footer e wp_footer
add_action('admin_footer', 'add_wc_product_search_bar');
add_action('wp_footer', 'add_wc_product_search_bar');

// Função AJAX para buscar produtos
add_action('wp_ajax_search_wc_products', 'search_wc_products');
function search_wc_products() {
    $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';

    if ($query) {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 10,
            's' => $query,
        );

        $products = new WP_Query($args);

        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();

                // Pega a URL da imagem destacada (thumbnail)
                $product_image = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                if (!$product_image) {
                    $product_image = wc_placeholder_img_src(); // Imagem de placeholder se não houver imagem
                }

                // Exibe o título do produto com a imagem
                echo '<a href="' . esc_url(get_edit_post_link(get_the_ID())) . '">';
                echo '<img src="' . esc_url($product_image) . '" alt="' . esc_attr(get_the_title()) . '" style="width:50px;height:auto;vertical-align:middle;margin-right:10px;">';
                echo get_the_title();
                echo '</a>';
            }
        } else {
            echo '<p>Nenhum produto encontrado.</p>';
        }

        wp_reset_postdata();
    }

    wp_die();
}
