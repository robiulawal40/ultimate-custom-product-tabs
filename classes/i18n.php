<?php 
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Searcheo_Tracker
 * @subpackage Searcheo_Tracker/includes
 * @author     Your Name <email@example.com>
 */
if( !class_exists('UCPT_i18n') ):
class UCPT_i18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {
        // echo "<pre>";
        // print_r( dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        // );
        // echo "</pre>";
        // exit;
        load_plugin_textdomain(
            'wotb',
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );

    }
}

$plugin_i18n = new UCPT_i18n();

add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ) );
endif;