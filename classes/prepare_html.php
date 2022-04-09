<?php 

if( !class_exists('UCPT_prepare_html') ):

    class UCPT_prepare_html{
        /*
         * Plugin constructor
         */
        function __construct() {

        }

        public function prepare_html($tabs){
            foreach( $tabs as $tab ){

                $content = $tab['content'];

                echo "<div class='options_group'>";
                echo "<div class='tab_row'>";
                echo "<div class='input_area'>";
                woocommerce_wp_text_input( array(
                    'id'                => 'ucpt_tabs_title_'.$tab['id'],
                    'name'              => 'ucpt_tabs['.$tab['id'].'][title]',
                    'value'             => $tab['title'],
                    'label'             => 'Tab Title',
                    'class'             => 'short tabs_fields'
                    ) );  
                woocommerce_wp_hidden_input( array(
                    'id'              => 'ucpt_tabs['.$tab['id'].'][id]',
                    'value'             => $tab['id'],
                    'class'             => 'short tabs_fields'
                    ) ); 
                echo '<p class="form-field">
                <label for="'.'ucpt_tabs_content_'.$tab['id'].'">Tab Content</label><textarea class="short tabs_fields enable_editor" style="" name="'.'ucpt_tabs['.$tab['id'].'][content]'.'" id="'.'ucpt_tabs_content_'.$tab['id'].'" placeholder="" rows="2" cols="20">'.$content.'</textarea> </p>'; 

                echo "</div>";
                echo "<div>";
                echo '<button type="button" data-tab_id="'.$tab['id'].'" class="button delete_tab" >Delete</button>';
                echo "</div>";
                echo "</div>";
                echo "</div>";
                }
    
            // $content   = '';
            // $editor_id = 'editor_'.$tab_id; 
            
            // wp_editor( $content, $editor_id );
        }
    }
endif;