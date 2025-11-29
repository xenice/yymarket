<?php

function yymarket_woocommerce_support() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'yymarket_woocommerce_support' );


// 禁用 WooCommerce 默认样式
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );



function yymarket_direct_buy_redirect( $url ) {

    if ( isset($_GET['direct-buy']) && isset($_GET['add-to-cart']) ) {

        // 要购买的产品 ID
        $product_id = intval($_GET['add-to-cart']);

        // 清空购物车
        WC()->cart->empty_cart();

        // 再将该产品加入购物车
        WC()->cart->add_to_cart($product_id);

        // 跳转到结算页
        return wc_get_checkout_url();
    }

    return $url;
}
add_filter( 'woocommerce_add_to_cart_redirect', 'yymarket_direct_buy_redirect' );


function yy_get_price( $product_id, $formatted = true ) {
    $product = wc_get_product( $product_id );
    
    if ( ! $product ) {
        return false;
    }

    if ( $formatted ) {
        return $product->get_price_html(); // 带货币符号和样式
    } else {
        return $product->get_price(); // 纯数字
    }
}

function yy_get_field($post_id, $name){
    return get_post_meta($post_id, 'yy_' . $name, true);
}

function yy_get_checkout_url($product_id){
    return wc_get_cart_url() . '?direct-buy=1&add-to-cart=' . $product_id;
}



function yy_products_per_page( $cols ) {
    return yy_get('resource_quantity') ?: 12;
}
add_filter( 'loop_shop_per_page', 'yy_products_per_page', 20 );

function yy_modify_home_query( $query ) {
    if ( ! is_admin() && $query->is_main_query() && is_home() ) {
        $query->set( 'post_type', 'product' );
        $query->set( 'orderby', 'modified' );
        $query->set( 'posts_per_page', yy_get('resource_quantity') ?: 12 );
    }
}
add_action( 'pre_get_posts', 'yy_modify_home_query' );

function yy_sort_by_modified_date( $query ) {

    if ( ! is_admin() && $query->is_main_query() ) {
        $query->set( 'orderby', 'modified' );
        $query->set( 'order', 'DESC' );
    }
}
add_action( 'pre_get_posts', 'yy_sort_by_modified_date' );