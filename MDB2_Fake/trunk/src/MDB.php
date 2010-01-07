<?php
// +----------------------------------------------------------------------+
// | PHP versions 5                                                       |
// +----------------------------------------------------------------------+
// |                                                                      |
// +----------------------------------------------------------------------+
/**
 * データーベースマネージャ
 * @package   MDB
 * @author    Ike Tohru <ike.tohru@gmail.com>
 */

require_once("libs/DataBaseManager.php");

/**
 * ファクトリーパターン
 */
class MDB{
	public static function factory($dsn){
        	return new DataBaseManager($dsn);
	}
	public static function connect($dsn){
    		return self::factory($dsn);
	}
}

?>
