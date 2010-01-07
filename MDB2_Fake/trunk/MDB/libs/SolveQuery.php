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

require_once("Query.php");

/**
 * ファクトリーパターン
 */
class SolveQuery{
	final public static function factory($db_type, $result){
		if($db_type === "mysql"){
			return new Query($db_type, $result);
		}
		else{
			throw new Exception("Error: db_type is not mysql");
		}
	}
}

?>
