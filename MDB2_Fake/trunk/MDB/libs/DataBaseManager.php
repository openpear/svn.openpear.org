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

require_once("SolveQuery.php");
require_once("DB.php");

/**
 * データーベースマネージャのインターフェース
 * @package   MDB
 * @author    Ike Tohru <ike.tohru@gmail.com>
 */
interface IMDB{
	public function __init__($dsn);     //
	public function open();             //データーベース接続を開く
	public function close();            //データーベース接続を閉じる
	public function free_connection();  //データーベース接続の破棄
	public function query($query);      //sqlの実行
	public function beginTransaction(); //トランザクション開始
	public function rollback();         //ロールバック
	public function commit();           //コミット
}

/**
 * データーベースクラスの抽象クラス メンバ変数とアクセサーの実装を行う。
 * @package   MDB
 * @author    Ike Tohru <ike.tohru@gmail.com>
 * @abstract 
 */
abstract class AbstractMDB{
	private $dsn = Array();         // DSN
	private $flg_connected = null;
	
	// Database type
	final protected function set_db_type($db_type){
		$this->dsn["db_type"] = $db_type;
	}
	final protected function get_db_type(){
		return $this->dsn["db_type"];
	}
	
	// Database connection string
	final protected function set_db_connection($db_connection){
		$this->dsn["db_connection"] = $db_connection;
	}
	final protected function get_db_connection(){
		return $this->dsn["db_connection"];
	}
	
	// The database username
	final protected function set_db_username($db_username){
		$this->dsn["db_username"] = $db_username;
	}
	final protected function get_db_username(){
		return $this->dsn["db_username"];
	}
	
	// The database password
	final protected function set_db_password($db_password){
		$this->dsn["db_password"] = $db_password;
	}
	final protected function get_db_password(){
		return $this->dsn["db_password"];
	}
	
	// Database server
	final protected function set_db_server($db_server){
		$this->dsn["db_server"] = $db_server;
	}
	final protected function get_db_server(){
		return $this->dsn["db_server"];
	}
	
	// The database being connected to
	final protected function set_db_database($db_database){
		$this->dsn["db_database"] = $db_database;
	}
	final protected function get_db_database(){
		return $this->dsn["db_database"];
	}
	
	// Determines if connection is established
	final protected function set_flg_connected($flg_connected){
		$this->flg_connected = $flg_connected;
	}
	final protected function get_flg_connected(){
		return $this->flg_connected;
	}
	
}

/**
 * データベースマネージャ
 * @package  MDB
 * @author   Ike Tohru <ike.tohru@gmail.com>
 * @param    string $dsn dsn
 * @return   void
 * @see      AbstractDBM, iDBM
 */
class DataBaseManager extends AbstractMDB implements IMDB{
	
	/**
	 * コンストラクタ
	 * @param  string $dsn dsn
	 * @return void
	 * @access public
	 */
	public function __construct($dsn){
		if(!$dsn){
			throw new Exception("Error:dsn is empty.");
		}
		
		$this->__init__($dsn);
	}
	
	/**
	 * コンストラクタの実体
	 * @param  string $dsn dsn
	 * @return void
	 * @access public
	 */
	final public function __init__($dsn){
		if(!$dsn){
			throw new Exception("Error:dsn is empty.");
		}
		
		$result = $this->__analyse_dsn__($dsn);
		
		$db_type = $result[0];
		$db_username = $result[1];
		$db_password = $result[2];
		$db_server = $result[3];
		$db_database = $result[4];
		
		$this->set_db_type($db_type);
		$this->set_db_username($db_username);
		$this->set_db_password($db_password);
		$this->set_db_server($db_server);
		$this->set_db_database($db_database);
		$this->set_flg_connected(false);
		
		//unset($result);
	}
	
	/**
	 * DataSourceNameの値を解析する
	 * @param  string $dsn dsn
	 * @return Array 0 db_type
	 *               1 db_username
	 *               2 db_password
	 *               3 db_server
	 *               4 db_database
	 * @access public
	 */
	final protected function __analyse_dsn__($dsn){
		if(!$dsn){
			throw new Exception("Error:dsn is empty.");
		}
		
		$db_type = explode("://", $dsn);
		$user = explode(":", $db_type[1]);
		$pass_w = explode("@", $user[1]);
		$sv = explode("/", $pass_w[1]);
		
		$result = Array();
		$result[0] = $db_type[0];
		$result[1] = $user[0];
		$result[2] = $pass_w[0];
		$result[3] = $sv[0];
		$result[4] = $sv[1];
		
		return $result;
	}
	
	/**
	 * データーベース接続の作成
	 * @param  void
	 * @return boolean true  接続成功
	 *                 false 接続失敗
	 * @access public
	 */
	final public function new_connection(){
		
		$db_connection = null;
		$db_type = $this->get_db_type();
		$db_username = $this->get_db_username();
		$db_password = $this->get_db_password();
		$db_server = $this->get_db_server();
		$db_database = $this->get_db_database();
		$flg_connected = $this->get_flg_connected();
		
		//check connected flag.
		if($flg_connected){
			throw new Exception("Error:DB Already connected!");
		}
		
		try{
			// Create connection to MYSQL database
			// Fourth true parameter will allow for multiple connections to be made
			$db_connection = DB::connect($db_type, $db_server, $db_username, $db_password, true);
			$this->set_db_connection($db_connection);
			
			//select database.
			DB::select_db($db_type, $db_database);
			
			if(!$db_connection){
				throw new Exception('Error:MySQL Connection Database Error: ' . mysql_error());
			}
			else{
				$this->set_flg_connected(true);
				return true;
			}
		}
		catch (Exception $e){
			die($e->getMessage());
		}
	}
	
	/**
	 * データーベース接続を開く
	 * @param  void
	 * @return boolean true  成功
	 *                 false 失敗
	 * @access public
	 */
	final public function open(){
		
		$db_connection = null;
		$db_type = $this->get_db_type();
		$db_username = $this->get_db_username();
		$db_password = $this->get_db_password();
		$db_server = $this->get_db_server();
		$db_database = $this->get_db_database();
		$flg_connected = $this->get_flg_connected();
		
		if($flg_connected){
			throw new Exception("Error:No connection has been established to the database. Cannot open connection.");
		}
		
		try{
		  $db_connection = DB::connect($db_type, $db_server, $db_username, $db_password, true);
			$this->set_db_connection($db_connection);
			
			//Select Database.
			DB::select_db($db_type, $db_database);
			
			if(!$db_connection){
				throw new Exception('MySQL Connection Database Error: ' . mysql_error());
			}
			else{
				$this->set_flg_connected(true);
				return true;
			}
		}
		catch (Exception $e){
			die($e->GetMessage());
		}
	}
	
	/**
	 * データーベース接続を閉じる
	 * @param  void
	 * @return boolean 
	 * @access public
	 */
	final public function close(){
		
		$db_type = $this->get_db_type();
		$db_connection = $this->get_db_connection();
		$flg_connected = $this->get_flg_connected();
		
		if(!$flg_connected){
			throw new Exception("Error:No connection has been established to the database. Cannot close connection.");
        }
		
		DB::close($db_type, $db_connection);
		$this->set_flg_connected(false);
		return true;
	}
	
	/**
	 * データーベース接続の破棄
	 * @param  void
	 * @return void
	 * @access public
	 */
	final public function free_connection(){
		$this->db_connection = null;
		$this->set_flg_connected(false);
	}
	
	/**
	 * sqlの実行
	 * @param  string $query 実行するクエリ
	 * @return mixed  連想配列の入れ子を返す
	 * @access public
	 */
	final public function query($query){
		
		$db_type = $this->get_db_type();
		
		if(!$query){
			throw new Exception("Error:query is empty.");
		}
		
		$db_connection = $this->get_db_connection();
		if(!$db_connection){
			throw new Exception("DB Error: [DB not connected!]");
		}
		
		$result = DB::query($db_type, $query, $db_connection);
		if(!$result){
			throw new Exception("Error Invalid query: " . mysql_error());
		}
		
		return SolveQuery::factory($db_type, $result);
		
	}
	
	/**
	 * トランザクション開始
	 * @param  void
	 * @return boolean True  トランザクション開始成功
	 *                 False トランザクション開始失敗
	 * @access public
	 */
	final public function beginTransaction(){
		
		$db_connection = $this->get_db_connection();
		if(!$db_connection){
			throw new Exception("DB Error: [DB not connected!]");
		}
		
		$result = $this->query("START TRANSACTION", $db_connection);
		if(!$result){
			throw new Exception("Error Invalid query: " . mysql_error());
		}
		
		return true;
	}
	
	/**
	 * ロールバック
	 * @param  void
	 * @return boolean True  ロールバック成功
	 *                 False ロールバック失敗
	 * @access public
	 */
	final public function rollback(){
		
		$db_connection = $this->get_db_connection();
		if(!$db_connection){
			throw new Exception("DB Error: [DB not connected!]");
		}
		
		$result = $this->query("ROLLBACK", $db_connection);
		if(!$result){
			throw new Exception("Error Invalid query: " . mysql_error());
		}
		
		return true;
	}
	
	/**
	 * コミット
	 * @param  void
	 * @return boolean True  コミット成功
	 *                 False コミット失敗
	 * @access public
	 */
	final public function commit(){
		
		$db_connection = $this->get_db_connection();
		if(!$db_connection){
			throw new Exception("DB Error: [DB not connected!]");
		}
		
		$result = $this->query("COMMIT", $db_connection);
		if(!$result){
			throw new Exception("Error Invalid query: " . mysql_error());
		}
		
		return true;
	}
	
	/**
	 * SQL文中で用いる文字列の特殊文字をエスケープする
	 * @param  string  $unescaped_string
	 * @return string  成功：エスケープ後の文字列
	 *         boolean 失敗：false
	 * @access public
	 */
	final public function escape($unescaped_string){
		
		$db_type = $this->get_db_type();
		$db_connection = $this->get_db_connection();
		
		if($unescaped_string === ""){}
		else if($unescaped_string === 0){}
		else if(!$unescaped_string){
            throw new Exception("Error: unescaped_string is empty");
		}
		
		$escaped_string = DB::escape($db_type, $unescaped_string, $db_connection);
		
		return $escaped_string;
	}

}

?>
