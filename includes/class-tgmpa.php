<?php
/**
 * Class MyTheme_TGMPA
 *
 * 封装 TGM Plugin Activation 的初始化与多语言支持
 */
class YYMarket_TGMPA {

    /**
     * 构造函数：绑定必要钩子
     */
    public function __construct() {
        if(!is_admin()) return;
        // 加载 TGM 库（如果存在）
        $this->load_tgmpa_library();

        // 注册插件需求
        add_action( 'tgmpa_register', array( $this, 'register_required_plugins' ) );

        // 加载 TGM 多语言支持
        add_action( 'init', array( $this, 'load_tgmpa_textdomain' ), 1);
        
    }

    /**
     * 加载 TGM 插件激活库
     */
    private function load_tgmpa_library() {
        $file = __DIR__ . '/tgm/class-tgm-plugin-activation.php';
        if ( file_exists( $file ) ) {
            require_once $file;
        }
    }

    /**
     * 注册必需/推荐的插件
     */
    public function register_required_plugins() {
        $plugins = array(
            array(
                'name'      => 'WooCommerce',
                'slug'      => 'woocommerce',
                'required'  => true,
            ),
        );

        $config = array(
            'id'           => 'mytheme',
            'default_path' => '',
            'menu'         => 'tgmpa-install-plugins',
            'has_notices'  => true,
            'dismissable'  => true,
            'dismiss_msg'  => '',
            'is_automatic' => false,
            'message'      => '',
        );

        tgmpa( $plugins, $config );
    }

    /**
     * 为 TGM 加载主题自带的语言包（支持多语言）
     */
    public function load_tgmpa_textdomain() {
        if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
            return;
        }

        $locale = get_locale();
        $mofile = __DIR__ . '/tgm/languages/tgmpa-' . $locale . '.mo';


        if ( file_exists( $mofile ) ) {
            load_textdomain( 'tgmpa', $mofile );
        }

    }
    


}
