<?php

class Router_seo
{
    public $action;

    public $path;

    public $requests;

    public $application;

    public function build()
    {

        if ( isset($_SERVER['argc']) && $_SERVER['argc'] != 0 ) {
            $uri = $_SERVER['argv'][1];

        } elseif( $_SERVER['PATH_INFO'] != "" ) {
            $uri = $_SERVER['PATH_INFO'];

        } else {
            $uri = DEFAULT_ACTION;
        }

        $list = explode( '/', $uri );
        if ( substr( $uri, 0, 1 ) == '/' ) array_shift($list);

        for( $i = 0; $i < count($list); $i += 2 ) {
            $args[$list[$i]] = @$list[$i+1];
        }

        $this->action      = @$args['action'];
        $this->path        = @$args['path'];
        $this->requests    = array_merge( $_POST, $args );
        $this->application = $args['app'];

        return;
    }
}
