<?php

class Router_default
{
    public $action;

    public $path;

    public $requests;

    public $application;

    /**
     * URLパース
     * 
     * #test
     * <code>
     * #true(#f());
     * </code>
     *
     * @return void
     * @access public
     */
    public function build()
    {
        if( isset($_SERVER["HTTP_HOST"]) ) {
            if ( $_SERVER['REQUEST_URI'] ) {
                $uri = $_SERVER['REQUEST_URI'];
            } else {
                $uri = DEFAULT_ACTION;
            }
        } else {
            $uri = $_SERVER['argv'][1];
        }

        preg_match( '/^(.*)(TEST)$/', $uri, $match );
        if ( isset($match[2]) ) {
            $this->test_flag = true;
            $uri = $match[1];
        }

        // 
//print "$uri<br>";
        preg_match_all( "#/([\w_-]+)(?=/)#is", $uri, $match );
//print nl2br(print_r($match,true));exit;
        $arg = str_replace( '/'.implode( '/', $match[1] ).'/', '', $uri );
//print "$arg<br>";
        parse_str( $arg, $args );
//print nl2br(print_r($args,true));exit;

        $this->action      = array_pop($match[1]);
        //$this->application = APPLICATION_NAME;
        $this->application = array_shift($match[1]);
        $this->path        = implode( '/', $match[1] );
        $this->requests    = array_merge( $_POST, $args );
//print "action ".     $this->action      ."<br>";
//print "path ".       $this->path        ."<br>";
//print "requests ".   $this->requests    ."<br>";
//print "application ".$this->application ."<br>";
        return;
    }
}
