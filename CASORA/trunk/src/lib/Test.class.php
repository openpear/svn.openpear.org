<?php
require_once(LIB_DIR.'/FW/Test/iTest.interface.php');

class Test
{
    private static $instance = array();

    private function __construct() {}

    public static function getInstance( $type )
    {

        if ( ! isset(self::$instance[$type] ) ) {

            $filename = LIB_DIR . '/FW/Test/'. strtolower($type) .'.class.php';

            if ( is_readable( $filename ) ) {
                require_once( $filename );
            } else {
                throw new Exception("Nothing test type. ($filename)");
            }
            
            $classname = 'Test_'. strtolower($type);

            self::$instance[$type] = new $classname();
        }

        return self::$instance[$type];
    }

}
