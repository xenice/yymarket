<?php
class WP_Optimizer {

    public function __construct() {
        add_action('init', [$this, 'init']);
    }

    public function init() {
        // 1. 禁用 Gutenberg（经典编辑器）
        add_filter('use_block_editor_for_post', '__return_false');
        add_filter('use_block_editor_for_post_type', '__return_false');

        // 2. 移除 Gutenberg 样式（可选，保持干净）
        add_action('wp_print_styles', function () {
            wp_dequeue_style('wp-block-library');
        });
        add_action('admin_print_styles', function () {
            wp_deregister_style('wp-block-library');
        });

        // 3. 启用经典小部件（仅当未安装官方插件时）
        if (!function_exists('classic_widget_plugin_init')) {
            remove_theme_support('widgets-block-editor');
        }

        // 4. 禁用修订版本
        add_filter('wp_revisions_to_keep', '__return_zero');
        
        add_filter( 'post_thumbnail_html', [$this, 'remove_image_attribute'], 10 );
        add_filter( 'image_send_to_editor', [$this, 'remove_image_attribute'], 10 );
    }
    
    public function remove_image_attribute( $html ) {
        // 匹配 img 标签，捕获 src 和 alt（alt 可能不存在）
        $pattern = '/<img\s+([^>]*?)src\s*=\s*["\']([^"\']+)["\'][^>]*?(?:alt\s*=\s*["\']([^"\']*)["\'])?[^>]*>/i';
    
        // 替换为只包含 src 和 alt（如果存在）
        $html = preg_replace_callback($pattern, function ($matches) {
            $src = esc_url($matches[2]);
            $alt = isset($matches[3]) ? esc_attr($matches[3]) : '';
            return '<img src="' . $src . '" alt="' . $alt . '" />';
        }, $html);
    
        return $html;
    }
}
