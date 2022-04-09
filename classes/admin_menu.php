<?php 
if( !class_exists('Admin_Menu') ):

    class Admin_Menu{

        public function __construct(){
            add_action( 'admin_menu', array( $this, 'register_menu' ) );
        }

        public function register_menu() {

            $parent_name = 'BaseCamp Setting';
            $parent_slug = UCPT_DOMAIN;
            $submenu_1 = __("Mail Setting", "wotb");
            $submenu_1_slug="mail_setting";
    
            add_menu_page(
                __( $parent_name, 'wotb' ),
                $parent_name,
                'manage_options',
                $parent_slug,
                array( $this, 'html_callback' ),
                'dashicons-screenoptions',
                66
            );
    
            add_submenu_page( $parent_slug, $parent_name, $parent_name, 'manage_options', $parent_slug );
    
            add_submenu_page( $parent_slug, $submenu_1, $submenu_1, 'manage_options', $submenu_1_slug, array( $this, 'callback_submenu_1' ) );
            // add_submenu_page( $parent_slug, 'Email Settings', 'Email Settings', 'manage_options', 'email-settings', array( $this, 'email_settings' ) );
            // add_submenu_page( $parent_slug, 'Uber Eats or other', 'Uber Eats or other', 'manage_options', 'uber-eats-other', array( $this, 'uber_eats_others' ) );
            // add_submenu_page( $parent_slug, 'Settings', 'Settings', 'manage_options', 'general-settings', array( $this, 'general_settings' ) );
    
            // if( current_user_can("branch") ){
            //     $parent_slug = 'branch-settings';
            //     add_menu_page(
            //         __( 'Branch Settings', 'multibranch-store' ),
            //         'Branch Settings',
            //         'branch',
            //         $parent_slug,
            //         array( $this, 'branch_settings' ),
            //         'dashicons-screenoptions',
            //         66
            //     );
            // }
            add_action( 'admin_init', array( $this,'register_my_cool_plugin_settings') );
    
        }
        public function html_callback() {
            // if ( is_file( plugin_dir_path( __FILE__ ) . 'admin/pages/branches.php' ) ) {
            //     include_once plugin_dir_path( __FILE__ ) . 'admin/pages/branches.php';
            // }
        //     <table class="form-table">
        //     <tr valign="top">
        //     <th scope="row">PopUP ShortCode</th>
        //     <td>
        //         <input type="text" readonly id="short_code" name="short_code" value="[mbs_popup/]" />
        //     </td>
        // </tr>
        // </table>
           echo '<div class="wrap">
                    <h2></h2>
                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-1">
                            <div id="post-body-content">
                                <div id="the_view" style="max-width: 500px; margin: auto;"></div>
                                <style>
                                .components-base-control .components-base-control__help {
                                    margin-top: 2px;
                                }
                                </style>
                            </div>
                        </div>
                        <br class="clear">
                    </div>
                </div>';
        }

        public function callback_submenu_1(){
        // Set class property
        // $this->options = get_option( 'my_option_name' );
        if(!current_user_can( 'manage_options' )){
            return;
          }
        ?>
        <div class="wrap">
            <h1><?php _e("E-mail Settings", "wotb"); ?></h1>
            <?php settings_errors(); ?>
            <?php
            
            if(isset($_POST['order_id'], $_POST['mail'])){
            $order_id = sanitize_text_field($_POST['order_id']);
            $email = sanitize_text_field($_POST['mail']);
            if((new UCPT_Send_Email($order_id))->set_test_email($email)->send()){
                // echo " to: ".$email;
                add_settings_error( 'test_mail', 'mbs_message', __( 'Email send Successfully for Order id:'.$order_id.' e-mail:'. $email, 'wotb' ), 'success' );
            }else{
                add_settings_error( 'test_mail', 'mbs_message', __( 'Failed to send mail for Order id:'.$order_id.' e-mail:'. $email, 'wotb' ), 'error' );
            }
            }
            ?>
            <?php settings_errors("test_mail"); ?>
            <form method="post">
                <table class="form-table">
                        <tr valign="top">
                        <th scope="row"><?php _e("Test", "wotb"); ?></th>
                        <td>
                            <input type="hidden" name="page"  value="mail_setting" />
                            <input type="text" class="small" name="order_id" placeholder="order_id" value="" />
                            <input type="text" class="small" name="mail" placeholder="e-mail" value="" />
                            <button type="submit" class="button button-secondary">Send Test email</button>
                    </td>
                        </tr>               
                    </table>
            </form>

            <form method="post" action="options.php">
            <?php 
            if ( isset( $_GET['settings-updated'] ) ) {
                // add settings saved message with the class of "updated"
                add_settings_error( 'wotb_option_group', 'mbs_message', __( 'Settings Saved', 'woocommerce' ), 'updated' );
            }
            ?>
            <table class="form-table">
                <tr valign="top">
                <th scope="row"><?php _e("Variables for each order", "wotb"); ?></th>
                <td class="variables"> 
                    <b>{order_id}</b> 
                    <b>{order_key}</b>
                    <b>{customer_id}</b>
                    <b>{billing_first_name}</b>
                    <b>{billing_last_name}</b>
                    <b>{billing_company}</b>
                    <b>{billing_address_1}</b>
                    <b>{billing_address_2}</b>
                    <b>{billing_city}</b>
                    <b>{billing_state}</b>
                    <b>{billing_postcode}</b>
                    <b>{billing_country}</b>
                    <b>{billing_email}</b>
                    <b>{billing_phone}</b>
                    <b>{shipping_first_name}</b>
                    <b>{shipping_last_name}</b>
                    <b>{shipping_company}</b>
                    <b>{shipping_address_1}</b>
                    <b>{shipping_address_2}</b>
                    <b>{shipping_city}</b>
                    <b>{shipping_state}</b>
                    <b>{shipping_postcode}</b>
                    <b>{shipping_country}</b>
                    <b>{payment_method_title}</b>
                    <b>{customer_note}</b>
                    <b>{date_completed}</b>
                    <b>{formatted_billing_full_name}</b>
                    <b>{formatted_shipping_full_name}</b>
                    <b>{formatted_billing_address}</b>
                    <b>{formatted_shipping_address}</b>
                </td>
                </tr>
        </table>
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'wotb_option_group' );
                do_settings_sections( 'mail_setting' );
                echo '<h2>'.__("Email 1 - Message for Developer & Design product", "wotb").'<h2>';
            ?>
            <table class="form-table">
                <tr valign="top">
                <th scope="row"><?php _e("To", "wotb"); ?></th>
                <td><input type="text" class="regular-text" name="wotb_email_1_to" value="<?php echo esc_attr( get_option('wotb_email_1_to') ); ?>" /></td>
                </tr>

                <tr valign="top">
                <th scope="row"><?php _e("Subject", "wotb"); ?></th>
                <td><input type="text" class="regular-text" name="wotb_email_1_subject" value="<?php echo esc_attr( get_option('wotb_email_1_subject') ); ?>" /></td>
                </tr>                
            </table>
            <?php 
                // $content=  html_entity_decode(get_option('wotb_email_1_content'));
                // echo "<pre>";
                // print_r($content);
                // var_dump($content);
                // echo "</pre>";

                wp_editor(  __(html_entity_decode(get_option('wotb_email_1_content'))), "wotb_email_1_content", $settings = array('wpautop'=>false) );
            ?>

            <br />
            <br />
                <?php 
                echo '<h2>'.__("Email 2 - Message for Design product", "wotb").'<h2>';
                ?>
                <table class="form-table">
                    <tr valign="top">
                    <th scope="row"><?php _e("To", "wotb"); ?></th>
                    <td><input type="text" class="regular-text" name="wotb_email_2_to" value="<?php echo esc_attr( get_option('wotb_email_2_to') ); ?>" /></td>
                    </tr>
    
                    <tr valign="top">
                    <th scope="row"><?php _e("Subject", "wotb"); ?></th>
                    <td><input type="text" class="regular-text" name="wotb_email_2_subject" value="<?php echo esc_attr( get_option('wotb_email_2_subject') ); ?>" /></td>
                    </tr>                
                </table>
                <?php 
                // $content=  html_entity_decode(get_option('wotb_email_2_content'));
                // echo "<pre>";
                // print_r($content);
                // var_dump($content);
                // echo "</pre>";

                    wp_editor(  __(html_entity_decode(get_option('wotb_email_2_content'))), "wotb_email_2_content", $settings = array('wpautop'=>false) );
                ?>                
            <?php
                submit_button();
            ?>
            </form>
        </div>
        <style>
            .variables b{
                border: 1px solid #130b121f;
                padding: 5px;
                border-radius: 10px;
                margin-right: 10px;
                line-height: 39px;
            }
            </style>
        <?php
        }

        public function register_my_cool_plugin_settings() {
            //register our settings
            register_setting( 'wotb_option_group', 'wotb_email_1_to', ['sanitize_callback' => [$this, 'sanitize_text_field']] );
            register_setting( 'wotb_option_group', 'wotb_email_1_subject', ['sanitize_callback' => [$this, 'sanitize_text_field']]  );
            register_setting( 'wotb_option_group', 'wotb_email_1_content', ['sanitize_callback' => [$this, 'sanitize_content']] );
            register_setting( 'wotb_option_group', 'wotb_email_2_to', ['sanitize_callback' => [$this, 'sanitize_text_field']] );
            register_setting( 'wotb_option_group', 'wotb_email_2_subject', ['sanitize_callback' => [$this, 'sanitize_text_field']] );
            register_setting( 'wotb_option_group', 'wotb_email_2_content', ['sanitize_callback' => [$this, 'sanitize_content']] );
        }

        public function sanitize_content($content){
            //save this in the database
            // $_content=sanitize_text_field( htmlentities($content) );
            $_content=( htmlentities($content) );
            return $_content;
            //to display, use
            //html_entity_decode($content);
        }

        public function sanitize_text_field($text_field){

            $_text_field=sanitize_text_field( htmlentities($text_field) );
            return $_text_field;
        }
    }
new Admin_Menu();
endif;