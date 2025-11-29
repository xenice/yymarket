<?php
/**
 * Class YY_Product_Custom_Fields
 *
 * Adds and saves custom product fields in WooCommerce admin.
 */
class YY_Product_Custom_Fields {

    /**
     * Constructor: Hook into WooCommerce actions.
     */
    
    
    public function __construct() {
        add_action('woocommerce_init', [$this, 'init_hooks']);
    }
    
    public function init_hooks() {
        add_action( 'woocommerce_product_options_general_product_data', [ $this, 'add_custom_fields' ] );
        add_action( 'woocommerce_process_product_meta', [ $this, 'save_custom_fields' ] );
    }
    
    /**
     * Add custom fields to the General tab of the product edit page.
     */
    public function add_custom_fields() {
        global $post;

        echo '<div class="options_group">';

        // Verify Code
        woocommerce_wp_text_input( [
            'id'          => 'yy_verify_code',
            'label'       => __( 'Verify code', 'onenice' ),
            'placeholder' => '',
            'desc_tip'    => true,
            'description' => __( 'Enter the verification code for this product.', 'onenice' ),
            'value'       => get_post_meta( $post->ID, 'yy_verify_code', true ),
        ] );

        // Version
        woocommerce_wp_text_input( [
            'id'          => 'yy_version',
            'label'       => __( 'Version', 'onenice' ),
            'placeholder' => '',
            'desc_tip'    => true,
            'description' => __( 'Product version number.', 'onenice' ),
            'value'       => get_post_meta( $post->ID, 'yy_version', true ),
        ] );

        // Free Download URL
        woocommerce_wp_textarea_input( [
            'id'          => 'yy_free_download_url',
            'label'       => __( 'Free download url', 'onenice' ),
            'placeholder' => '',
            'desc_tip'    => true,
            'description' => __( 'URL for free download (visible to authorized members).', 'onenice' ),
            'value'       => get_post_meta( $post->ID, 'yy_free_download_url', true ),
            'rows'        => 3,
        ] );

        // Demo URL
        woocommerce_wp_textarea_input( [
            'id'          => 'yy_demo_url',
            'label'       => __( 'Demo url', 'onenice' ),
            'placeholder' => '',
            'desc_tip'    => true,
            'description' => __( 'URL to product demo or preview.', 'onenice' ),
            'value'       => get_post_meta( $post->ID, 'yy_demo_url', true ),
            'rows'        => 3,
        ] );

        // Service Information
        woocommerce_wp_textarea_input( [
            'id'          => 'yy_service_info',
            'label'       => __( 'Service infomation', 'onenice' ),
            'placeholder' => '',
            'desc_tip'    => true,
            'description' => __( 'Multiple entries are separated by commas.', 'onenice' ),
            'value'       => get_post_meta( $post->ID, 'yy_service_info', true ),
            'rows'        => 3,
        ] );

        echo '</div>';
    }

    /**
     * Save custom field values when the product is saved.
     *
     * @param int $post_id The product post ID.
     */
    public function save_custom_fields( $post_id ) {
        // Verify code
        if ( isset( $_POST['yy_verify_code'] ) ) {
            update_post_meta( $post_id, 'yy_verify_code', sanitize_text_field( wp_unslash( $_POST['yy_verify_code'] ) ) );
        }

        // Version
        if ( isset( $_POST['yy_version'] ) ) {
            update_post_meta( $post_id, 'yy_version', sanitize_text_field( wp_unslash( $_POST['yy_version'] ) ) );
        }

        // Free download URL
        if ( isset( $_POST['yy_free_download_url'] ) ) {
            $url = esc_url_raw( wp_unslash( $_POST['yy_free_download_url'] ) );
            update_post_meta( $post_id, 'yy_free_download_url', $url );
        }

        // Demo URL
        if ( isset( $_POST['yy_demo_url'] ) ) {
            $url = esc_url_raw( wp_unslash( $_POST['yy_demo_url'] ) );
            update_post_meta( $post_id, 'yy_demo_url', $url );
        }

        // Service info
        if ( isset( $_POST['yy_service_info'] ) ) {
            $service_info = sanitize_textarea_field( wp_unslash( $_POST['yy_service_info'] ) );
            update_post_meta( $post_id, 'yy_service_info', $service_info );
        }
    }
}

