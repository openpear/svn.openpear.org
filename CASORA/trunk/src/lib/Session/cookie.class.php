<?php

// Todo 未完成

class Session_cookie extends Session implements SessionInterface
{
    
    public function initialize()
    {
        parent::initialize();

        ini_set("session.use_cookies", 1 );
        ini_set("session.use_only_cookies", 1 );
        ini_set("session.use_trans_sid", 0 );
    }

    public function __construct( $property )
    {
        //foreach( $property as $key => $val ) $this->$key = $val;

        $this->initialize();
    }

    public function _open( $save_path, $session_name )
    {
//print "open";

        if ( isset($_COOKIE) ){
            if ( isset($_COOKIE[session_name()]) ){
                session_id( $_COOKIE[session_name()] );
                $this->session_id = session_id();
            }
        	return true;
        } else {
            return false;
        }
    }

    public function _close()
    {
//print "close";

        //$this->deleteInstance();
    }

    public function _read( $id )
    {
//print "read";
        return @$_COOKIE[$id];
    }

    public function _write( $id, $data )
    {
//print "write";
        $result = headers_sent() ? false : setcookie( $id, $data, time() + SESS_CACHE_EXPIRE * 60, '/' );

        return $result;
    }

    public function _destroy( $id )
    {
        if ( isset($_COOKIE[session_name()]) ) {
            setcookie( session_name(), "", time() - 42000, '/' );
        }
        if ( isset($_COOKIE[$id]) ) {
	        setcookie( $id, "", time() - 42000, '/' );
        }
    }

    public function _clean( $max )
    {
//print "clean";
        foreach( $_SESSION as $key => $val ){
            setcookie( $key, "", time() - 42000, '/' );
        }
    }


    public function getSessionId()
    {
        return $this->session_id;
    }


}
