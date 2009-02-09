<?php


class Session_db extends Session implements SessionInterface
{

    private $db;

    public function initialize()
    {
        parent::initialize();

        ini_set("session.use_cookies", 0 );
        ini_set("session.use_only_cookies", 0 );
        //ini_set("session.use_trans_sid", 0 );
        //ini_set("session.session.save_path", '' );
    }

    public function __construct( $property )
    {
        //foreach( $property as $key => $val ) $this->$key = $val;

        $this->initialize();
    }


    public function _open( $save_path, $session_name )
    {
        Logger::to()->debug("Call function is Session::_open($save_path, $session_name)");
//print "$save_path, $session_name = ";
        //if ( isset($_REQUEST[$session_name] ) ) {
        //    $this->db = DB::getInstance( DB::SLAVE );
        //    $expire_date = time() - $this->gc_maxlifetime;
        //    $sql = "SELECT variables FROM session_history WHERE session_id = '".$_REQUEST[$session_name]."' AND login_date >= '$expire_date'";
        //    $data = $this->db->query( $sql )->fetch( PDO::FETCH_ASSOC );

        //    if ( $data ){
        //        $_SESSION = $data[];
        //    }
        //}
    }

    public function _close()
    {
        Logger::to()->debug("Call function is Session::_close()");

        $this->db = null;
    }

    public function _read( $id )
    {
        Logger::to()->debug("Call function is Session::_read($id)");

        $this->db = DB::getInstance( DB::SLAVE );

        //$this->db->easy_mapper( 'select', 'session_hihstory', array('variables'), array( 'session_id' => $id) );
        $expire_date = time() - self::$gc_maxlifetime;
        $sql = "SELECT variables FROM session_history WHERE delete_flag = 0 AND session_id = '$id' AND login_date >= '$expire_date'";
        $item = $this->db->query( $sql )->fetchObject();

        if( $item ){
            return $item->variables;
        } else {
            return false;
        }

    }

    public function _write( $id, $data )
    {
        Logger::to()->debug("Call function is Session::_write($id,$data)");

        $this->db = DB::getInstance( DB::MASTER );

        $result = $this->db->easy_mapper( 'replace', 'session_history', array( 'session_id' => $id, 'variables' => $data, 'login_date' => time(), 'delete_flag' => 0 ) );

        if ( ! $result ) {
            throw new Exception('セッションの更新に失敗しました',11);
        }

        //$this->{$this->name} = $id;

        return true;
    }

    public function _destroy( $id )
    {
        Logger::to()->debug("Call function is Session::_destroy($id)");

        $this->db = DB::getInstance( DB::MASTER );

        $this->db->easy_mapper( 'update', 'session_history', array( 'variables' => null, 'delete_flag' => 1 ), array( 'delete_flag' => 0, 'session_id' => $id ) );

        if ( ! $result ) {
            throw new Exception('セッションの更新に失敗しました',11);
        }

        return true;
    }

    public function _clean( $max )
    {
        Logger::to()->debug("Call function is Session::_clean($max)");

        $this->db = DB::getInstance( DB::MASTER );

        $time = time() - $max;
        $sql = "UPDATE session_history SET delete_flag = 1 WHERE delete_flag = 0 AND login_date < $time";

        return $this->db->exec( $sql );
    }

}
