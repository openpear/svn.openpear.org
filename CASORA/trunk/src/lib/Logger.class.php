<?php
/**
 * ロガークラス
 *
 * @author $Author: ryun $
 * @package lib
 * @copyright Copyright(c) 2007 RyuN Corp.
 **/

require_once('Log.php');

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
 **/
class Logger {
    /**
     * 出力先ハンドル
     * @access public
     * @var string
     **/
    public static $Handler  = 'file';//console, syslog, mail, etc..

    /**
     * ログファイル名
     * @access public static
     * @var string
     **/
    public static $Filename = '/var/log/php_script.log';

    /**
     * ident名
     * @access public static
     * @var string
     **/
    public static $Ident    = 'php_script';

    /**
     * フォーマット設定配列
     * @access public
     * @access static
     * @var array
     **/
    public static $Conf     = array('timeFormat' => '%Y-%m-%d %H:%M:%S',
                                    'lineFormat' => '[%1$s] %2$s [%3$s] %5$s:%6$s:%7$s() %4$s');

    /**
     * 出力エラーレベル
     * @access public
     * @var string
     **/
    public static $Level    = PEAR_LOG_WARNING;

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
     * ロギング実行
     *
     * ログを出力する
     *
     * @access public
     * @param $ident カテゴリ名
     * @return void
     **/
    public static function to( $ident = "" ){
        if( $ident == "" ){
            //$bt = debug_backtrace();
            //preg_match('/.+\/(.+?)$/',$bt[0]['file'],$match);
            //$ident = $match[1];

            //$ident = self::$Ident;

            $ident = getmypid();
        }
        //return Log::factory(self::$Handler,self::$Filename,$ident,self::$Conf,self::$Level);
        return @Log::singleton(self::$Handler,self::$Filename,$ident,self::$Conf,self::$Level);
    }

}
?>
