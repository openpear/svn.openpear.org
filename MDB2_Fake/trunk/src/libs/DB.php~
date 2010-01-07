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

/**
 * @package  MDB
 */
class DB{
	final static public function connect($db_type, $server, $username, $password, $new_link){
		if($db_type === "mysql"){
			return mysql_connect($server, $username, $password, $new_link);
		}
		else{
			throw new Exception("Error: db_type is not mysql");
		}
	}
	final static public function select_db($db_type, $database_name){
		if($db_type === "mysql"){
			return mysql_select_db($database_name);
		}
		else{
			throw new Exception("Error: db_type is not mysql");
		}
	}
	final static public function query($db_type, $query){
		if($db_type === "mysql"){
			return mysql_query($query);
		}
		else{
			throw new Exception("Error: db_type is not mysql");
		}
	}
	final static public function fetch_assoc($db_type, $result){
		if($db_type === "mysql"){
			return mysql_fetch_assoc($result);
		}
		else{
			throw new Exception("Error: db_type is not mysql");
		}
	}
	final static public function escape($db_type, $unescaped_string, $link_identifier){
		if($db_type === "mysql"){
			return mysql_real_escape_string($unescaped_string, $link_identifier);
		}
		else{
			throw new Exception("Error: db_type is not mysql");
		}
	}
	final static public function close($db_type, $link_identifier){
		if($db_type === "mysql"){
			return mysql_close($link_identifier);
		}
		else{
			throw new Exception("Error: db_type is not mysql");
		}
	}
}

?>
