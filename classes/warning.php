<?php
if ( !class_exists( 'UCPT_Warning' ) ):
    class UCPT_Warning{

        public function __construct(){
            $this->error = new WP_Error();
        }

        public function add($error_message, $code="unknown" ){
            $this->error->add($code, $error_message);
            return $this;
        }

        public function remove( $code ) {
            $this->error->remove($code);
            return $this;
        }

        public function has_errors(){
            return $this->error->has_errors();
        }

        public function get_error_message( $code = '' ){
            return $this->error->get_error_message( $code );
        }

        public function get_error_messages( $code = '' ){
            return $this->error->get_error_messages( $code );
        }

        public static function log( $entry, $mode = 'a', $file = 'plugin' ) {

            $upload_dir = __DIR__;

            if ( is_array( $entry ) ) {
                $entry = json_encode( $entry );
            }

            $file  = $upload_dir . '/' . $file . '.log';
            $file  = fopen( $file, $mode );
            $bytes = fwrite( $file, date("Y-m-d H:i:s",(current_time( 'timestamp', 1 )+ 21600)) . "::" . $entry . "\n" );
            fclose( $file );
    
            return $bytes;
        }

    }
endif;