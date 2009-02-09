<?php



class Session_memcached extends Session implements SessionInterface
{

    private $session_id;

    public function Session_memcached( $property )
    {
        foreach( $property as $key => $val ) $this->$key = $val;

        ini_set("session.save_handler", "memcache" );
        ini_set("session.save_path", $this->save_path );
        ini_set("session.use_cookies", 0 );

    }

    public function Start()
    {
        session_start();
        $this->session_id = session_id();
    }

    public function Close()
    {
        session_write_close();
        $this->deleteInstance();
    }

    public function Destroy()
    {
        $_SESSION = array();
        session_destroy();
    }

    public function getSessionId()
    {
        return $this->session_id;
    }
}
