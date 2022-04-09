<?php 

if( !class_exists('UCPT_enqueue_scripts') ):

    class UCPT_enqueue_scripts{
        /*
         * Plugin constructor
         */
        function __construct() {
            // add_action( 'init', array( $this, 'register_scripts' ) );
            // add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
            add_filter('script_loader_tag', array($this, 'add_type_attribute') , 10, 3);

        }

        public function add_type_attribute($tag, $handle, $src) {
            // if not your script, do nothing and return original $tag
            if ( 'tabs-module' !== $handle ) {
                return $tag;
            }
            // change the script tag by adding type="module" and return it.
            $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
            return $tag;
        }

        public function register_scripts() {

        }

        /**
         * @return null
         */
        public function load_scripts() {

            global $post, $wpdb;
        }
         
        /**
         * @param $hook
         */
        public function admin_scripts( $hook ) {
            global $post_type, $post;

            // if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
            //     if ( 'infinite_gallery' === $post_type ) {
            //         if ( !did_action( 'wp_enqueue_media' ) ) {
            //             wp_enqueue_media();
            //         }
            //         wp_enqueue_style( 'wotb-admin-style', UCPTURL . "assets/style.admin.css", array(), UCPT_VERSION );

            //         wp_enqueue_style( 'wp-color-picker' );

            //         wp_enqueue_script( 'wotb-admin-scripts', UCPTURL . "assets/scripts.admin.js", array( 'jquery', 'thickbox', 'wp-color-picker' ), UCPT_VERSION, true );
            //         wp_enqueue_style( 'thickbox' );
            //         wp_localize_script(
            //             'wotb-admin-scripts',
            //             'wotb',
            //             array(
            //                 'ajax_url' => admin_url( 'admin-ajax.php' ),
            //                 'nonce'    => wp_create_nonce( 'wotb_attachment_to_gallery' ),
            //                 'post_id'  => $post->ID,
            //             )
            //         );

            //     }
            // }
            // echo "<pre>";
            //      print_r($hook);
            // echo "</pre>";
            // exit;

            if ( "toplevel_page_wotb" == $hook ) {
                // wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'admin-setting-scripts', plugins_url( '/dist/index.js', dirname( __FILE__ ) ), array( 'wp-api', 'wp-i18n', 'wp-components', 'wp-element' ), null, true );

                wp_enqueue_style( 'wotb-admin-style',plugins_url( '/dist/style-index.css', dirname( __FILE__ ) ), array('wp-components'), UCPT_VERSION );

                    wp_localize_script(
                        'admin-setting-scripts',
                        'wotb',
                        array(
                            'url' => home_url("/wp-json/{$this->api_namespace}"),
                            'nonce'    => wp_create_nonce( 'wp_rest' ),
                        )
                    );
            }

            if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
                if ( 'product' === $post_type ) {

                // wp_enqueue_script("tabs", WP_CONTENT_URL."/build/tabs.js", array( 'wp-api', 'wp-i18n', 'wp-components', 'wp-element' ), null, true );
                wp_enqueue_script("tabs-module", WP_CONTENT_URL."/src/tabs/tabs.js", array( 'wp-api', 'wp-i18n', 'wp-components', 'wp-element' ), null, true );
                   
                // wp_enqueue_script( 'admin-setting-scripts-meta-box', plugins_url( '/dist/meta-box.js', dirname( __FILE__ ) ), array( 'wp-api', 'wp-i18n', 'wp-components', 'wp-element' ), null, true );
                wp_enqueue_editor();
                wp_enqueue_script( 'admin-tabs', plugins_url( '/assets/scripts.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-blockui'), null, true );

                wp_enqueue_style( 'admin-style',plugins_url( '/assets/admin_style.css', dirname( __FILE__ ) ), array(), UCPT_VERSION );

                wp_localize_script(
                    'admin-tabs',
                    'ucpt',
                    array(
                        'ajax_url' => admin_url( 'admin-ajax.php' ),
                        'nonce'    => wp_create_nonce( 'ucpt' ),
                        'post_id'    => $post->ID,
                    )
                );
                }
            }
        }



    }

endif;