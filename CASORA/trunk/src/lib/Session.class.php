<?php
require_once(dirname(__FILE__).'/Session/interface.php');

/**
 * 
 *
 * 
 * 
 *
 * 例
 * <code>
 *   require_once('DB.class.php');
 *
 *   DB::setParamater( array( 'Master' => array( DB_MASTER ),
 *                            'Slave'  => array( DB_SLAVE ),
 *                            'Type'   => DB_TYPE,
 *                            'Name'   => DB_NAME,
 *                            'User'   => DB_USER,
 *                            'Pass'   => DB_PASS ) );
 *
 *   $DB = DB::getInstance( DB::SLAVE );
 *   $DB->prepare('select * from credit');
 *   $DB->execute();
 *   $DB->fetchAll(PDO::FETCH_OBJ);
 * </code>
 *
 * @version 0.1
 * @author $ryun$
 * @package lib
 * @copyright Copyright(c) RyuN Corp.
 **/
class Session {


    /**
     * シングルトンオブジェクトを格納する変数
     **/
    private static $singleton = array();

    private $_session_id = null;

    /**
     * 
     **/
    private static $save_handler = NULL;
    /**
     * 
     **/
    private static $save_path = NULL;
    /**
     * 
     **/
    protected static $use_cookies = 0;
    /**
     * 
     **/
    protected static $use_trans_sid = 1;
    /**
     * 
     **/
    protected static $name = 'PHPSESSID';
    /**
     * 
     **/
    protected static $cache_expire  = NULL;
    /**
     * 期限切れ時間（秒）
     **/
    protected static $gc_maxlifetime  = NULL;


    public static function setParamater( $param ){

        foreach( $param as $key => $val ){
            if( property_exists( get_class(), $key ) ) {
                self::$$key = $val;
            }
        }
    }

    public function initialize(){


        ini_set("session.save_handler",   "user");
//        ini_set("session.cache_expire",   self::$cache_expire );
        ini_set("session.gc_maxlifetime", self::$gc_maxlifetime );
//        ini_set("session.name",           self::$name );

        session_set_save_handler( array(&$this, '_open'),
                                  array(&$this, '_close'),
                                  array(&$this, '_read'),
                                  array(&$this, '_write'),
                                  array(&$this, '_destroy'),
                                  array(&$this, '_clean')
                                );
    }

    /**
     * インスタンスを生成
     * 
     * 
     * @access public
     * @return object
     **/
    public static function getInstance( $handler = null )
    {

        if ( self::$singleton[$handler] == null ) {

            Logger::to()->info('Choice session '. $handler);
            $handler = strtolower($handler);
            $class = 'Session_' . $handler;
            $classfile = LIB_DIR. '/FW/Session/' . $handler . '.class.php';

            if ( is_readable($classfile) ) {
                require_once($classfile);
            } else {
                Logger::to()->warning( "not found file $classfile" );
            }

            if ( class_exists($class) ) {
                //foreach( get_class_vars(get_class()) as $key => $val ) $property[$key] = self::$$key;
                //self::$singleton[$handler] = new $class( $property );
                self::$singleton[$handler] = new $class(null);

            } else {
                Logger::to()->warning( "no exists class $class" );
            }
        }

        return self::$singleton[$handler];
        //return session_id($session_id);
    }

    public function start( $session_id = null )
    {

        if ( $session_id != null ) session_id($session_id);

        session_start();

        If ( $session_id == null ) session_regenerate_id();

        $this->setSessionId( session_id() );

        return true;
    }

    public function close()
    {        
        session_write_close();
        $this->setSessionId(null);

        $this->initialize();
    }

    public function destroy()
    {
        foreach( $_SESSION as $key => $value) unset($_SESSION[$key]);
        session_destroy();
        $this->setSessionId(null);

    }

    public function setSessionId( $session_id )
    {        
        $this->_session_id = $session_id;
    }
    public function getSessionId()
    {        
        return $this->_session_id;
    }

    /**
     *  
     * 
     * 
     * 
     * @access public
     * @return boolean
     **/
    public static function choiceHandler(){

        //return 'cookie'; // とりあえずcookie縛り

        $url = parse_url(self::$save_path);

        if ( isset($url['host']) && isset($url['port']) ) {
            if( @memcache_connect($url['host'], $url['port']) ) {
                return 'memcached';
            }
        }

        return 'db';

    }
}

?>
