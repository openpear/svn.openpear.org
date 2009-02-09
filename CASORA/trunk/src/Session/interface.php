<?php


interface SessionInterface
{
    public function initialize();
    public function _open( $save_path, $session_name );
    public function _close();
    public function _read( $id );
    public function _write( $id, $data );
    public function _destroy( $id );
    public function _clean( $max );

}
