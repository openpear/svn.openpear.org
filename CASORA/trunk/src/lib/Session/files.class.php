<?php

// Todo 未完成

class Session_files extends Session implements SessionInterface
{

    private $save_path;

    public Session_normal()
    {
        //ini_set("session.save_handler", "user");
        //ini_set("session.save_path", "/tmp");

        //session_set_save_handler( array(&$this, '_open'),
        //                          array(&$this, '_close'),
        //                          array(&$this, '_read'),
        //                          array(&$this, '_write'),
        //                          array(&$this, '_destroy'),
        //                          array(&$this, '_clean'),
        //                        );

    }

    public function _open( $save_path, $session_name )
    {
        $this->save_path = $save_path;
        return true;
    }

    public function _close()
    {
        return true;
    }

    public function _read( $id )
    {
        return @file_get_contents($this->save_path."/sess_$id");
    }

    public function _write( $id, $sess_data )
    {
        return @file_put_contents($this->save_path."/sess_$id", $sess_data, LOCK_EX );

    }

    public function _destroy( $id )
    {
        return @unlink($this->save_path."/sess_$id");
    }

    public function _clean( $maxlifetime )
    {
        foreach( glob( $this->save_path ."/sess_*" ) as $filename ) {
            if ( filemtime($filename) + $maxlifetime < time() ) {
                @unlink($filename);
            }
        }

        return true;
    }

}
