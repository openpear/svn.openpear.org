<?php

class Error
{
    const REPORT_SYSLOG = 0;
    const REPORT_EMAIL = 1;
    const REPORT_DISPLAY = 2;
    const REPORT_FILE = 4;
    const REPORT_ALL = 7;

    private static $_default_status = array (
                E_ERROR              => 'Error',
                E_WARNING            => 'Warning',
                E_PARSE              => 'Parsing Error',
                E_NOTICE             => 'Notice',
                E_CORE_ERROR         => 'Core Error',
                E_CORE_WARNING       => 'Core Warning',
                E_COMPILE_ERROR      => 'Compile Error',
                E_COMPILE_WARNING    => 'Compile Warning',
                E_USER_ERROR         => 'User Error',
                E_USER_WARNING       => 'User Warning',
                E_USER_NOTICE        => 'User Notice',
                E_STRICT             => 'Runtime Notice',
                E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
            );

    private static $_old_error_handler;
    private static $_report_level;
    private static $_callback = null;
    private static $_param;

    private function __construct() {}

    public static function execute( $report_level, $param )
    {
        self::$_report_level = $report_level;

        self::$_param = $param;

        self::$_old_error_handler = set_error_handler( array( &$this, 'userErrorHandler' ) );
    }

    public static function restore()
    {
        restore_error_handler();
    }

    private static function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars)
    {

        if ( self::$_report_level > $errno && ! isset(self::$_param[$errno]) ) {
            return true;
        }

        extract( self::$_param[$errno] );


        if ( $report_type & self::REPORT_SYSLOG ) {
            error_log($err, 0);
        }

        if ( $report_type & self::REPORT_DISPLAY ) {
            echo "<b>". self::$_default_status[$errno] ."</b> [$errno] $errstr<br />\n";
            echo "  Fatal error on line $errline in file $errfile";
        }

        if ( $report_type & self::REPORT_FILE ) {
            error_log($err, 3, "/usr/local/php4/error.log");
        }

        if ( $report_type & self::REPORT_EMAIL ) {
            mail("phpdev@example.com", "Critical User Error", $err);
        }

        if ( $callback ) {
            call_user_func( $callback , $errno, $errmsg, $filename, $linenum, $vars );
        }

        return true;
    }
}
?>
