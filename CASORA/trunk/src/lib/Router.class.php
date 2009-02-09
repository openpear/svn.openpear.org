<?php

class Router
{
    private static $instance = array();

    private function __construct() {}

    public static function getInstance( $type )
    {

        if ( ! isset(self::$instance[$type] ) ) {

            $filename = LIB_DIR . '/FW/Router/'. strtolower($type) .'.class.php';

            if ( is_readable( $filename ) ) {
                require_once( $filename );
            } else {
                throw new Exception("Nothing router type. ($filename)");
            }
            
            $classname = 'Router_'. strtolower($type);

            self::$instance[$type] = new $classname();
        }

        return self::$instance[$type];
    }

}
