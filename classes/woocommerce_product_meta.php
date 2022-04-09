<?php
if( ! class_exists('UCPT_woocommerce_product_meta') ):
class UCPT_Woocommerce_Product_Meta {

    use UCPT_sanitization;

    public function __construct() {

        $this->tabs = [];
      
        add_filter('woocommerce_product_data_tabs', array($this, 'product_settings_tabs') );
        add_action( 'woocommerce_product_data_panels', array($this, 'ucpt_product_panels') );

        add_action( 'woocommerce_process_product_meta', array($this, 'save_fields'), 10, 2 );

        add_action("wp_ajax_get_empty_tab", array($this, "get_empty_tab"));
        add_action("wp_ajax_get_tabs", array($this, "get_tabs"));
        add_action("wp_ajax_save_tabs", array($this, "save_tabs"));
        add_action("wp_ajax_delete_tabs", array($this, "delete_tabs"));
    }

    public function product_settings_tabs( $tabs ){
 
        //unset( $tabs['inventory'] );
     
        $tabs['ucpt'] = array(
            'label'    => __('Ultimate Tabs', 'wcpt'),
            'target'   => 'ucpt_product_data',
            'class'    => array('show_if_virtual', 'show_if_simple', 'show_if_variable', 'show_if_grouped', 'show_if_external'),
            'priority' => 1000,
        );
        return $tabs;
     
    }

    public function save_tabs(){

        $posted_tabs = $this->sanitize_text_or_array_field($_POST['data']);
        $post_id     = $this->sanitize_text_or_array_field($_POST['post_id']);

        $data = "";
        foreach( $posted_tabs as $field ){
            $data .="&";
            $data .=$field['name']."=".$field['value'];
        }
        parse_str($data, $output);

         update_post_meta( $post_id, 'ucpt_tabs', $output['ucpt_tabs'] );
        $this->tabs = get_post_meta($post_id, 'ucpt_tabs', true );
        wp_send_json($this->tabs);
    }

    public function delete_tabs(){
        $this->set_post_id( sanitize_text_field($_POST['post_id']) )->set_tabs()->delete(sanitize_text_field($_POST['delete_tab_id']) );
        $this->send_updated_html();
    }

    public function get_tabs(){

        $this->set_post_id( sanitize_text_field($_POST['post_id']) )->set_tabs();

        ob_start();
            ( new UCPT_prepare_html() )->prepare_html( $this->tabs );
        $result = ob_get_clean();

       wp_send_json($this->tabs);
    }

    public function get_empty_tab(){

        $this->set_post_id( sanitize_text_field($_POST['post_id']) )->update_tabs()->set_tabs()->add_empty_tab_data();

        ob_start();
        ( new UCPT_prepare_html() )->prepare_html( $this->tabs );
    $result = ob_get_clean();

   wp_send_json($result); 
    }

    public function send_updated_html(){
        $this->set_tabs();
              ob_start();
                ( new UCPT_prepare_html() )->prepare_html( $this->tabs );
    $result = ob_get_clean();

   wp_send_json($result); 

    }

    public function ucpt_product_panels(){

        $this->set_post_id( sanitize_text_field($_GET['post']) )->set_tabs();
        
        echo '<div id="ucpt_product_data" class="panel woocommerce_options_panel">';

        echo '<div class="prepare_input_html">';
        ( new UCPT_prepare_html() )->prepare_html( $this->tabs );
        echo '</div>';

       echo '<div class="toolbar">
				<button type="button" class="button-primary add_new_tabs" >Add new Tabs</button>
				<button type="button" class="button-primary save_tabs" >Save changes</button>
				<button type="button" class="button" >Cancel</button>
			</div>';

        echo '</div>';
     
    }
    public function set_post_id($id){
        $this->post_id = $id;
        return $this;
    }
    public function set_tabs(){
        $ucpt_tabs = get_post_meta($this->post_id, 'ucpt_tabs', true );
        if( $ucpt_tabs && is_array($ucpt_tabs) ){
            $this->tabs = $ucpt_tabs;
        }else{
            $this->tabs = [
                [
                    "id"   => 2,
                    "title"=>"The Title Will Go Here",
                    "content"=>"The Content Will Go Here"
                ]
            ];
        }
        return $this;
    }

    public function delete($tab_id){

       $new_tab =  array_filter($this->tabs, function($tab) use($tab_id){
            if( $tab_id != $tab['id'] ){
                return true;
            }
            return false;
        });
        update_post_meta( $this->post_id, 'ucpt_tabs', $new_tab );
        return $this;
    }

    public function update_tabs(){
        $posted_tabs = $this->sanitize_text_or_array_field($_POST['data']);

        $data = "";
        foreach( $posted_tabs as $field ){
            $data .="&";
            $data .=$field['name']."=".$field['value'];
        }
        parse_str($data, $output);

         update_post_meta( $this->post_id, 'ucpt_tabs', $output['ucpt_tabs'] );
         return $this;
    }

    public function add_empty_tab_data(){

        $max_id = array_reduce($this->tabs, 
                    function($carry, $item){
                        if( $item['id'] > $carry ){
                            return $item['id'];
                        }
                        return $carry;
                    }, 0);

         array_push($this->tabs, 
         [
            "id"=>$max_id+1,
            "title"=>"new pushed tab",
            "content"=>"pushed Conttent"
         ]);
         return $this;
    }

}
endif;
