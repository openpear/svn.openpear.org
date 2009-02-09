<?
/**
 * ロガークラス
 *
 * @author $Author: ryun $
 * @package lib
 * @copyright Copyright(c) 2008 RyuN Corp.
 **/


/**
 * ロガークラス
 *
 * Pearログクラスのラッパー。
 * 例
 * <code>
 * Log::to('test')->info('テストです');
 * 結果
 * [2006-12-19 00:10:01] test [info] テストです
 * </code>
 *
 * @package FW
 * @author   ryun@ryun.jp
 * @version   1.0
 * @copyright Copyright(c) 2009 RyuN Corp.
 **/
class Cookie{

    private static $singleton = null;

    /**
     * 出力先ハンドル
     * @access public
     * @var string
     **/
    public static $Expire = null;

    /**
     * ログファイル名
     * @access public static
     * @var string
     **/
    public static $Path = null;

    /**
     * ident名
     * @access public static
     * @var string
     **/
    public static $Domain = null;

    /**
     * フォーマット設定配列
     * @access public
     * @access static
     * @var array
     **/
    public static $Secure = null;

    /**
     * 出力エラーレベル
     * @access public
     * @var string
     **/
    public static $Httponly = null;

    /**
     * コンストラクタ
     * 
     * 
     * @access private
     * @return void
     **/
    private function __construct() {}


    public static function setParamater( array $param ){

        foreach( $param as $key => $val ){
            if( property_exists( get_class(), $key ) ) {
	        self::$$key = $val; 
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
    public static function getInstance() {

        if ( self::$singleton == null ) {
            self::$singleton = new Cookie();
        }

        return self::$singleton;
    }

    /**
     * ロギング実行
     *
     * ログを出力する
     *
     * @access public
     * @param $ident カテゴリ名
     * @return void
     **/
    public function set( $key, $value ){
        $value = urlencode($value);
        return setrawcookie( $key, $value, time() + self::$Expire, self::$Path, self::$Domain, self::$Secure, self::$Httponly );
    }

    public function get( $key = "" ){

        $result = null;

        if ($key != "") {
            if ( isset($_COOKIE[$key]) ) {
                $result = urldecode( $_COOKIE[$key] );
            }
        } else {
            foreach ($_COOKIE as $key => $val ) {
                $result[$key] = urldecode( $_COOKIE[$key] ); 
            }
        }

        return $result;
    }

    public function destroy( $key = "" ){

        $result = true;

        $list = ( $key == "" ) ? array($key) : array_keys($_COOKIE);

        foreach ($list as $key ) {
            $result &= setcookie( $key, "", time() - 3600, self::$Path, self::$Domain, self::$Secure, self::$Httponly );
        }

        return $result;
    }
}
?>
