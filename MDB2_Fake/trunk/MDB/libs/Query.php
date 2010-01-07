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

if (!defined('PATH_SEPARATOR')) {
  if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    define('PATH_SEPARATOR', ':');
  } else {
    define('PATH_SEPARATOR', ';');
  }
}
set_include_path(PATH_SEPARATOR);

require_once("DB.php");

/**
 * @package MDB
 * @author  Ike Tohru <ike.tohru@gmail.com>
 */
interface IQuery{
	public function fetchRow(); //一レコードを返す。
	public function fetchAll(); //すべてを返す。
	public function free();     //メモリを開放する。
}

/**
 * データーベースクラスの抽象クラス メンバ変数とアクセサーの実装を行う。
 * @package  MDB
 * @author   Ike Tohru <ike.tohru@gmail.com>
 * @abstract 
 */
abstract class AbstractQuery{
	
	private $db_type;       // DSN
	private $result = null;
	
	// Database type
	final protected function set_db_type($db_type){
		$this->db_type = $db_type;
	}
	final protected function get_db_type(){
		return $this->db_type;
	}
	
	final protected function set_result($result){
		$this->result = $result;
	}
	final protected function get_result(){
		return $this->result;
	}
	
}

/**
 * 
 * @package  MDB
 * @author   Ike Tohru <ike.tohru@gmail.com>
 * @param    void
 * @return   void
 * @see      iSolveDBResult
 */
class Query extends AbstractQuery implements IQuery{
	
	/**
	 * コンストラクタ
	 * @param  object $result DB result pointer
	 * @return void
	 * @access public
	 */
	public function __construct($db_type, $result){	
		$this->set_db_type($db_type);
		$this->set_result($result);
	}
	
	/**
	 * Fetch and return all rows from the current row pointer position
	 * a two dimensional array and then frees the result set.
	 * @param  void
	 * @return mixed
	 * @access public
	 */
	final public function fetchRow(){
		$db_type = $this->get_db_type();
		$result = $this->get_result();
		
		$row = DB::fetch_assoc($db_type, $result);
		return $row;
	}
	
	/**
	 * Fetch and return all rows from the current row pointer position
	 * @param  void
	 * @return mixed
	 * @access public
	 */
	final public function fetchAll(){
		$all = Array();
		while($row = $this->fetchRow()){
			$all[] = $row;
		}
		return $all;
	}
        
	/**
	 * Free the internal references so that the instance can be destroyed
	 * @return boolean    true on success
	 * @access public
	 */
	final public function free(){
		$this->set_result(null);
		return true;
	}
	
}

?>
