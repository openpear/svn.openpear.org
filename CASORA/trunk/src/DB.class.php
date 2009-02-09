<?php
/**
 * データベース接続クラス
 *
 * PDOのラッパー。
 * 実行ログを拾えるようにした。
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
class DB
{

    /**
     * モード定数（マスタ）
     **/
    const MASTER = 0;

    /**
     * モード定数（スレーブ）
     **/
    const SLAVE = 1;

    /**
     * デバッグ定数（オフ）
     **/
    const DEBUG_OFF = FALSE;

    /**
     * デバッグ定数（オン）
     **/
    const DEBUG_ON  = TRUE;


    /**
     * シングルトンオブジェクトを格納する変数
     **/
    private static $singleton = null;

    /**
     * コネクションハンドル
     **/
    private $DBh = array();

    /**
     * 実行結果変数
     **/
    private $Result = NULL;

    /**
     * prepare変数
     **/
    private $Prepare = NULL;

    /**
     * DBタイプ
     **/
    private static $Type = NULL;
    /**
     * DB名
     **/
    private static $Name = NULL;
    /**
     * マスタホスト
     **/
    private static $Master = NULL;
    /**
     * スレーブホストリスト
     **/
    private static $Slave = NULL;
    /**
     * DB接続ユーザ
     **/
    private static $User = NULL;
    /**
     * DBパスワード
     **/
    private static $Pass = NULL;
    /**
     * DB接続オプション
     **/
    private static $Option = array(PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
                                   PDO::ATTR_AUTOCOMMIT   => TRUE,
                                   PDO::ATTR_CASE         => PDO::CASE_LOWER,
                                   PDO::ATTR_ERRMODE      => PDO::ERRMODE_EXCEPTION);
    /**
     * 
     **/
    private static $_fromEncoding = NULL;
    /**
     * 
     **/
    private static $_toEncoding = NULL;

    /**
     * 接続ホスト
     **/
    private $Host = "";

    /**
     * 更新クエリチェック用正規表現
     **/
    private $MasterRegex = '/^(insert|update|delete|truncate|reprace)/i';

    /**
     * 接続モード(MASTER|SLAVE)
     **/
    public $Mode = null;

    /**
     * デバッグフラグ
     **/
    public static $Debug = FALSE;

    /**
     * コンストラクタ
     * 
     * 
     * @access private
     * @return void
     **/
    protected function __construct() {}

    public static function setParamater( array $param ){

        foreach( $param as $key => $val ){
            if( property_exists( get_class(), $key ) ) {
                if( ( $key == 'Master' || $key == 'Slave') && !is_array($val) ){
                    self::$$key = preg_split( '/[ \-#,\/]/', $val );
                } else {
                    self::$$key = $val;
                }
            }
        }

    }

    /**
     * インスタンスを生成
     * 
     * 
     * @access public
     * @return object
     **/
    public static function getInstance( $mode = null ) {

        if ( self::$singleton == null ) {
            //$class_name = get_class();
            $class_name = 'DB';
            self::$singleton = new $class_name();
        }

        if( $mode !== null ){
            self::$singleton->connect( $mode );
        }

        return self::$singleton;
    }

    /**
     * インスタンスを削除
     * 
     * 
     * @access public
     * @return object
     **/
    public function deleteInstance() {
        self::$singleton = null;
    }

    /**
     *  DB接続
     * 
     * 接続できなかった場合、他のホストに接続を試みる。
     * 
     * @access public
     * @return boolean
     **/
    public function connect( $mode ){

        $this->Mode = $mode;

        if ( isset($this->DBh[$mode]) ){
            if( is_resource($this->DBh[$mode]) ) return true;
        }

        // 接続先振り分け
        if( $mode == self::MASTER ) $hosts = is_array(self::$Master) ? self::$Master : array(self::$Master);
        if( $mode == self::SLAVE  ) $hosts = is_array(self::$Slave)  ? self::$Slave  : array(self::$Slave);

        // 接続先が無い場合
        if( count($hosts) == 0 ) return false;

        while( count($hosts) > 0 ){
            // ホストリストからランダムに接続するホストを選ぶ
            $pos = mt_rand( 0, count($hosts) - 1 );
            $this->Host = $hosts[$pos];

            try{
                // 接続
                $con = new PDO( self::$Type . ':dbname=' . self::$Name . ';host=' . $this->Host, self::$User, self::$Pass, self::$Option );
                if ( self::$_toEncoding ) $con->exec( 'SET NAMES '. self::$_toEncoding );

                break;

            // 接続失敗
            }catch( PDOException $e ){
                // 失敗したホストをリストから削除する
                array_splice( $hosts, $pos, 1 );

                // 接続するホストが無い場合
                if( count($hosts) == 0 ){

                    $message = 'There is no host who can connect it.( type='. self::$Type . ' dbname='. self::$Name .' host='. $this->Host .' user='. self::$User .' pass='. self::$Pass .' )';
                    trigger_error( $message, E_USER_ERROR );

                    return false;
                }
            }
        }

        if( self::$Debug ) {
            $message = 'Connect by '. self::$Type .':dbname='. self::$Name .';host='. $this->Host .'@'. self::$User;
            trigger_error( $message, E_USER_NOTICE );
        }

        $this->Result = $this->DBh[$mode] = $con;
        
        return true;
    }

    /**
     * メソッドのオーバーロード
     * 
     * スレーブに更新クエリ投げてないかチェック。<br>
     * メソッドが存在するかチェック。<br>
     * 実行オブジェクトの自動切り替えを行う。<br>
     * ログの収集を行う。
     * 
     * @access public
     * @return object
     **/
    public function __call( $method, $arg )
    {
        // 参照系に更新クエリを投げてないかチェック
        if( $method == 'query' || $method == 'prepare' ){

            $sql = $arg[0];

            if( preg_match( $this->MasterRegex, ltrim($sql) ) && $this->Mode == self::SLAVE ) {

                // 接続ハンドルチェック
                if( ! is_object( @$this->DBh[self::MASTER] ) ){

                    $message = "Cannot query executed by SLAVE.[$sql]";
                    trigger_error( $message, E_USER_WARNING );

                    $this->connect(self::MASTER);
                }
            }
        }


        // メソッドが存在するかチェック
        if( method_exists( $this->Prepare, $method ) ){
            $dbh = &$this->Prepare;

        }elseif( method_exists( $this->DBh[$this->Mode], $method ) ){
            $dbh = &$this->DBh[$this->Mode];

        }else{
            $message = "Call to undefined method DB->$method";
            trigger_error( $message, E_USER_ERROR );
            return false;
        }


        $time_start = $this->microtime_float();

        // メソッド呼び出し
        $var_name = ( $method == 'prepare' ) ? 'Prepare' : 'Result';

        try{
            $this->$var_name = call_user_func_array( array( $dbh, $method ), $arg );

        } catch ( Exception $e ) {
            $message = $e->getMessage().' [execute:'.$var_name.'->'.$method.'('.implode(',',$arg).')]';
            trigger_error( $message, E_USER_ERROR );
            return false;
        }

        $time_end = $this->microtime_float();

        if ( self::$Debug ) {
            $message = sprintf('%.10f',$time_end - $time_start) ."sec $method [". ($arg ? implode(' ',$arg) : '') ."]";
            trigger_error( $message, E_USER_NOTICE );
        }

        return $this->$var_name;


    }

    /**
     * メソッドの実行結果を取得
     * 
     * @access public
     * @return object
     **/
    public function getResult(){
        return $this->Result;
    }

    /**
     * 現在接続しているDBモードを取得
     * 
     * @access public
     * @return integer
     **/
    public function getMode(){
        return $this->Mode;
    }

    /**
     * 接続ハンドルを破棄する
     * 
     * 
     * @access public
     * @return void
     **/
    public function close() {
        $this->DBh = null;
    }

    /**
     * マイクロ秒を取得
     * 
     * 
     * @access private
     * @return float
     **/
    private function microtime_float() {
       list($usec, $sec) = explode(" ", microtime());
       return ((float)$usec + (float)$sec);
    }


    public function easy_mapper( $type, $table, array $columns, $wheres = array(), $order = array(), $group = array() ) {

        $name = array_keys($columns);

        switch( $type ){
        case 'select':
        case 'SELECT':
            $sql = "SELECT ". implode(',',$columns) ." FROM $table";
            $columns = $wheres;
            break;

        case 'insert':
        case 'INSERT':
        case 'replace':
        case 'REPLACE':
            $sql = strtoupper($type)." INTO $table( ". implode(',', $name). " ) ".
                                      //"VALUES(:". implode(',:',$name). " )";
                                      "VALUES(". implode(',',array_fill(1,count($name),'?')) .')';
            break;

        case 'update':
        case 'UPDATE':
            $sql = "UPDATE $table SET ";
            //foreach ( $name as $key ) $sql .= "$key = :$key,";
            foreach ( $name as $key ) $sql .= "$key = ?,";
            $sql = rtrim( $sql, "," );
            break;

        case 'delete':
        case 'DELETE':
            $sql = "DELETE FROM $table ";
            break;

        default:
            $message = 'no match sql type.';
            trigger_error( $message, E_USER_NOTICE );
        }

        if( count($wheres) ){
            $columns += $wheres;
            $sql .= " WHERE ";
            foreach( $wheres as $name => $val ){
                //$key = preg_replace( '/[`\'":!|&%#]/', '', $name );
                //$sql .= "$name = :$key AND ";
                $sql .= "$name = ? AND ";
            }
            $sql = substr( $sql, 0, -4);
        }

        if ( count($order) > 0 ) {
            $sql .= ' ORDER BY ' . implode( ',', $order );
        }

        if ( count($group) > 0 ) {
            $sql .= ' GROUP BY ' . implode( ',', $group );
        }

        $this->prepare( $sql );

        $i = 1;
        foreach( $columns as $name => $value ){
            //$name = preg_replace( '/[`\'":!|&%#]/', '', $name );
            //$this->bindParam( ":$name", $value );
            $this->bindParam( $i, $value );
            $i++;
        }

        return $this->execute();

    }

    public static function escapeStringSQL( $str ){
        return mysql_escape_string($str);
    }

}

class DBException extends Exception
{
    public function __construct( $message, $code = 0 ) {
        parent::__construct( $message, $code );
    }
}

?>
