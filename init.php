<?php 
new UCPT_i18n();
new UCPT_enqueue_scripts();
new UCPT_Woocommerce_Product_Meta();
new UCPT_prepare_html();
$GLOBALS['wcpt_error'] = new UCPT_Error();
$GLOBALS['wcpt_warning'] = new UCPT_Warning();

add_action('init', function(){
    if( $_SERVER['NODE'] ):
        
    global $aaa_error, $aaa_warning;
        
    
    print_r($aaa_error->get_error_message());
    print_r($aaa_warning->get_error_message());   

    //  $end_points =  new UCPT_Woocommerce_Actions();
     echo "\n\n\n\n";
      echo __FILE__.".php \n";
      print_r($aaa_error->get_error_message());
     //  $end_points->prepare_response([]);
     echo "\n"; 





     endif;
 });