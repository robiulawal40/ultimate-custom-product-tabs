<?php
/**
 *
 * @wordpress-plugin
 * Plugin Name:       Ultimate Custom Product Tabs for WooCommerce
 * Plugin URI:        https://github.com/robiulawal40/
 * Description:       This plugin extends WooCommerce to allow shop owners to add, remove and customize tabs to products. The tabs are displayed on the individual product pages based on product categories and tags or even single product page.
 * Version:           1.0.0
 * Author:            Robiul Awal
 * Author URI:        https://github.com/robiulawal40/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ucpt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

if ( !class_exists( 'Ultimate_Custom_Product_Tabs' ) ):

    final class Ultimate_Custom_Product_Tabs {

        /*
         * @var mixed
         */
        private static $instance;

        /*
         * instance functions
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new Ultimate_Custom_Product_Tabs;
            }
            return self::$instance;
        }

        /*
         * Cloning is forbidden.
         */
        public function __clone() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ucpt' ), '1.0' );
        }

        /*
         * Unserializing instances of this class is forbidden.
         */
        public function __wakeup() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ucpt' ), '1.0' );
        }

        /*
         * Plugin constructor
         */
        function __construct() {
            $this->text_domain = "UCPT";
            $this->set_constants();
            $this->includes();
        }

        /*
         * Setting the plugin  constant
         */
        public function set_constants() {

            if ( !defined( 'UCPT_VERSION' ) ) {
                define( 'UCPT_VERSION', '1.0.0' );
            }
            if ( !defined( 'UCPT_DOMAIN' ) ) {
                define( 'UCPT_DOMAIN', 'ucpt' );
            }
            if ( !defined( 'UCPT_NAME' ) ) {
                define( 'UCPT_NAME', 'Wc orders to Basecamp' );
            }
            if ( !defined( 'UCPTDIR' ) ) {
                define( 'UCPTDIR', plugin_dir_path( __FILE__ ) );
            }
            if ( !defined( 'UCPTBASENAME' ) ) {
                define( 'UCPTBASENAME', plugin_basename( __FILE__ ) );
            }
            if ( !defined( 'UCPTURL' ) ) {
                define( 'UCPTURL', plugin_dir_url( __FILE__ ) );
            }
            if ( !defined( 'UCPTDEV' ) ) {
                define( 'UCPTDEV', true );
            }
            if ( !defined( 'UCPT_IMAGES' ) ) {
                define( 'UCPT_IMAGES', '_UCPT_att' );
            }

        }

        /*
         * Plugin include files
         */
        public function includes() {


            spl_autoload_register(function( $class_name ) {

                if ( false !== strpos( $class_name, $this->text_domain ) ) {
                
                $classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;
                   
                $class_file = strtolower( str_replace(  $this->text_domain."_", "", $class_name) ) . '.php';
                  require_once $classes_dir . $class_file;
                }

              });
              require_once UCPTDIR . "init.php";


            // require_once UCPTDIR . "inc/shortcode.php";
            // require_once UCPTDIR . "admin/options.php";
            // require_once UCPTDIR . "admin/class-gallery-image-list-table.php";
            // require_once UCPTDIR . "admin/register-post.php";
        }


    }
endif;

function UCPT_init() {
    return Ultimate_Custom_Product_Tabs::instance();
}
add_action( 'plugins_loaded', 'UCPT_init' );