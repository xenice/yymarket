<?php


class YY_My_Account_Cleaner {

    public function __construct() {
        // 确保在 WooCommerce 初始化后执行
        add_action('woocommerce_init', [$this, 'init_hooks']);
    }

    public function init_hooks() {
        // 移除 My Account 页面中的 Addresses 菜单项
        add_filter( 'woocommerce_account_menu_items', [$this, 'remove_addresses_from_my_account_menu'] );
    }
    
    
    public function remove_addresses_from_my_account_menu( $items ) {
        unset( $items['edit-address'] ); // 移除地址菜单
        return $items;
    }


}
