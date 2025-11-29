<?php

class YY_Checkout_Cleaner {

    public function __construct() {
        // Declare incompatibility with Cart & Checkout Blocks before WooCommerce initializes
        add_action( 'before_woocommerce_init', [ $this, 'declare_block_incompatibility' ] );

        // Initialize other hooks after WooCommerce is ready
        add_action( 'woocommerce_init', [ $this, 'init_hooks' ] );
    }

    /**
     * Declare that this plugin is NOT compatible with WooCommerce Cart & Checkout Blocks.
     * This forces WooCommerce to use the classic checkout flow.
     */
    public function declare_block_incompatibility() {
        if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, false );
        }
    }

    public function init_hooks() {
        // Disable shipping for virtual-only carts
        add_filter( 'woocommerce_cart_needs_shipping', [ $this, 'disable_shipping_for_virtual' ] );
        add_filter( 'woocommerce_cart_needs_shipping_address', [ $this, 'disable_shipping_for_virtual' ] );

        // Simplify checkout fields for virtual products
        add_filter( 'woocommerce_checkout_fields', [ $this, 'remove_unnecessary_checkout_fields' ] );

        // Extra safeguard: ensure no shipping on cart page
        add_action( 'woocommerce_before_cart', [ $this, 'ensure_no_shipping_for_virtual' ] );

        // Hide "added to cart" message
        add_filter( 'wc_add_to_cart_message_html', '__return_false' );

        // Redirect cart directly to checkout
        add_action( 'template_redirect', [ $this, 'redirect_cart_to_checkout' ] );
        // Prevent redirect and show custom message
        add_action( 'template_redirect', [ $this, 'handle_empty_checkout_gracefully'], 5 );

        // Force classic checkout if block is present (fallback)
        add_action( 'template_redirect', [ $this, 'force_classic_checkout' ] );
    }

    /**
     * Always disable shipping (assumes store sells only virtual products).
     *
     * @param bool $needs_shipping
     * @return bool
     */
    public function disable_shipping_for_virtual( $needs_shipping ) {
        return false;
    }

    /**
     * Remove unnecessary billing fields when shipping is not needed.
     *
     * @param array $fields
     * @return array
     */
    public function remove_unnecessary_checkout_fields( $fields ) {
        if ( WC()->cart && ! WC()->cart->needs_shipping() ) {
            $fields_to_remove = [
                'billing_first_name',
                'billing_last_name',
                'billing_company',
                'billing_address_1',
                'billing_address_2',
                'billing_city',
                'billing_postcode',
                'billing_state',
                'billing_country', // Keep if needed
                'billing_phone',   // Keep if needed
            ];

            foreach ( $fields_to_remove as $field ) {
                unset( $fields['billing'][ $field ] );
            }
        }
        return $fields;
    }

    /**
     * Ensure shipping is disabled on the cart page (defensive measure).
     */
    public function ensure_no_shipping_for_virtual() {
        add_filter( 'woocommerce_cart_needs_shipping', '__return_false' );
    }

    /**
     * Redirect cart page directly to checkout.
     */
    public function redirect_cart_to_checkout() {
        if ( is_cart() ) {
            wp_safe_redirect( wc_get_checkout_url() );
            exit;
        }
        return;
    }
    
    public function handle_empty_checkout_gracefully() {
        if ( is_checkout() && ! is_wc_endpoint_url() && WC()->cart->is_empty() ) {
            // Stop WooCommerce's default redirect
            remove_action( 'woocommerce_checkout_before_order_review', 'wc_print_notices', 10 );
    
            // Optional: Add your own notice
            add_action( 'woocommerce_before_checkout_form', function() {
                wc_print_notice( 'Your cart is empty. Please add some products before checking out.', 'onenice' );
            });
    
            // Important: Do NOT redirect — let the page render
            // But we must prevent WooCommerce from redirecting later
            add_filter( 'woocommerce_checkout_redirect_empty_cart', '__return_false' );
            add_filter( 'woocommerce_cart_redirect_after_error', '__return_empty_string' );
        }
    }

    /**
     * Force classic checkout by replacing the WooCommerce Checkout block with shortcode,
     * in case the checkout page still uses the block despite compatibility declaration.
     */
    public function force_classic_checkout() {
        if ( ! is_checkout() || is_wc_endpoint_url() ) {
            return;
        }

        global $post;
        if ( ! $post || ! has_block( 'woocommerce/checkout', $post ) ) {
            return;
        }

        // Replace block content with classic shortcode
        add_filter( 'the_content', [ $this, 'replace_checkout_block_with_shortcode' ], 999 );
    }

    /**
     * Callback to replace checkout block with classic shortcode.
     *
     * @param string $content
     * @return string
     */
    public function replace_checkout_block_with_shortcode( $content ) {
        global $post;
        if ( $post && has_block( 'woocommerce/checkout', $post ) ) {
            return do_shortcode( '[woocommerce_checkout]' );
        }
        return $content;
    }
}