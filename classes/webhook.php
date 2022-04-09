<?php 

if( !class_exists("UCPT_Webhook") ):

    class UCPT_Webhook{

        use UCPT_Data;

        public function __construct(){
            add_action( 'rest_api_init', array($this,'register_url') );
            $this->request = new UCPT_API();
            $this->error = new WP_Error();            
        }

        public function log_errors(){
            if( $this->error->has_errors() ){
                $rand = rand(1, 100);
                foreach ( (array) $this->error->errors as $code => $messages ) {
                    // $all_messages = array_merge( $all_messages, $messages );
                    Log_Error::log($rand." Error_Code:".$code. "; Error_Message:". $messages[0], "a", "webhook");
                }
                
            }
        }
        public function handle_error(){
            if($this->request->the_response)
            print_r($this->request->the_response);
            $this->error->errors = array_merge($this->error->errors, $this->request->error->errors);            
            $this->log_errors();
        }
        public function register_url(){
            // ?code=cd7fdf5b  =(?P<code>\d+) WP_REST_Server::CREATABLE
            register_rest_route( $this->api_namespace, '/receive_verification_code', array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array($this, 'callback'),
                'args' => array(
                    'code' => array(
                        'type'        => 'string',
                        'description' => esc_html__( 'The filter parameter for code', 'wotb' ),
                    ),
                  ),
                'permission_callback' => array($this, 'permission_callback'),
            ) ); 
        }

        public function callback($data){

            $param = $data->get_param( 'code' );
            $this->set_verification_code($param);
            //Log_Error::log("Verification_Code: ".$param);
            $this->request_get_access_token();
            // print_r($this->get_admin_url());
            // echo '<script>// Simulate a mouse click:
            // window.location.href = "'.$this->get_admin_url().'";
            // window.location.replace("'.$this->get_admin_url().'");</script>';
            echo __("Verification Successful", "wotb");
            wp_safe_redirect($this->get_admin_url());
            return;
            // return wp_json_encode($param);  
            // return rest_ensure_response( 'Hello World, this is the WordPress REST API' );
        }

        public function permission_callback(){
            // if ( ! current_user_can( 'edit_posts' ) ) {
            //     return new WP_Error( 'rest_forbidden', esc_html__( 'OMG you can not view private data.', 'my-text-domain' ), array( 'status' => 401 ) );
            // }
            return true;
        }

        public function set_authentication_data($res_body){
            // $res_body = $this->request->res_body;

            $this->set_access_token($res_body['access_token']);
            // $_date = date("c", strtotime('+'.number_format($res_body['expires_in']).' seconds'));
            // $this->set_expires_in($_date);
            $this->set_refresh_token($res_body['refresh_token']);
            $this->authorization();
            print_r($res_body );
            Log_Error::log($res_body);
        }

        public function url_access_token(){
            return "https://launchpad.37signals.com/authorization/token?type=web_server&client_id={$this->get_client_id()}&redirect_uri={$this->get_redirect_uri()}&client_secret={$this->get_client_secret()}&code={$this->get_verification_code()}";
        }

        public function request_get_access_token(){
            // $request = new UCPT_API();
            $res = $this->request->set_endpoint($this->url_access_token())->post();
            if(!$res){
                $this->handle_error();
            }else{
                $res_body = $this->request->res_body;
                $this->set_authentication_data($res_body);

            }
            return 0;
        }

        public function url_refresh_token(){

            // token?type=refresh&refresh_token=your-current-refresh-token&client_id=your-client-id&redirect_uri=your-redirect-uri&client_secret=your-client-secret
            return "https://launchpad.37signals.com/authorization/token?type=refresh&refresh_token={$this->get_refresh_token()}&client_id={$this->get_client_id()}&redirect_uri={$this->get_redirect_uri()}&client_secret={$this->get_client_secret()}";
        }
        
        public function request_refresh_token(){
            if( !$this->is_expired() ){
                $this->error->add("Access Token Still not Expired:", "Access Token Still not Expired");
                $this->handle_error();
            }
            $res = $this->request->set_endpoint($this->url_refresh_token())->post();
            if(!$res){
                $this->error->add("Refresh Token Failed:", "Refresh token request failed");
                $this->handle_error();
            }else{
                $res_body = $this->request->res_body;
                $this->set_authentication_data($res_body);
            }
            return 0;
        }
        public function authorization(){
            $authorization_url = "https://launchpad.37signals.com/authorization.json";
            $res = $this->request->set_endpoint($authorization_url)->set_bearer_auth($this->get_access_token())->get();
            if(!$res){
                $this->handle_error();
            }else{
                $res_body = $this->request->res_body;
                if( isset($res_body['accounts'][0]['href'])){
                    $this->set_selected_account($res_body['accounts'][0]['href']);
                }
                if( isset($res_body['expires_at'])){
                    $this->set_expires_in($res_body['expires_at']);
                }
                if( isset($res_body['accounts']) && is_array($res_body['accounts']) ){
                    $_accounts = [];
                    foreach( $res_body['accounts'] as $account ){
                        $_accounts[ $account['href'] ] = $account['name']." (". $account['href'].")";
                    }
                    $this->set_all_accounts($_accounts);
                }

                if( isset($res_body['identity']) && is_array($res_body['identity']) ){
                    $this->set_basecamp_name($res_body['identity']['first_name']." ". $res_body['identity']['last_name']);
                }
                return $res_body; 
               }
        }

        public function is_expired(){
            // print_r($authorization);
            // $expires_at= $authorization['expires_at'];
            // echo date("Y-m-d H:i:s", strtotime($expires_at));
            // echo strtotime($expires_at);
            // echo "<br />";
            // echo strtotime('now')." : ";
            $expire_at = $this->get_expires_in();
            // echo date("c", strtotime('+'.number_format("1").' seconds'));
            // echo date("c", strtotime($expire_at));
            // echo "<br />".$expire_at;
            if(!empty($expire_at) && $expire_at){
                $_expire_at = strtotime($expire_at);
                $__expire_at = $_expire_at; // - ( 24 * 60 * 60 )
                $time_now = strtotime('now');
                if( $__expire_at < $time_now ){
                    return true;
                }else{
                    return false;
                }
                // echo $_expire_at." :";
                // echo $__expire_at;
                // echo "<br />";
            }else{
                return true;
            }
            // echo $expire_at;
        }

    }      
    $request = new UCPT_Webhook();
// echo $request->get_auth_url();
// echo $request->get_admin_url();
// echo $request->get_access_token();
// print_r($request->get_redirect_uri());
// print_r($request->authorization());
// print_r($request->get_basecamp_name());
// print_r($request->get_all_accounts());
// print_r($request->is_expired());
// $request->request_refresh_token();
// $time = $request->get_expires_in();
// $c_time = wp_get_current_timestamp();
// echo current_time('timestamp', 1);


endif;