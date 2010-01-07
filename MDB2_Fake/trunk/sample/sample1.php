<?php

//ライブラリの読み込み
require_once('../MDB/MDB.php');

//dsnの定義
define('DB_USER', 'root');        //データベースのユーザー名
define('DB_PASS', 'I8LNNOCrNh');  //データーベースのパスワード
define('DB_HOST', 'localhost');   //データーベースのホスト
define('DB_DATABASE', 'MDBTest'); //データーベースのデーターベース名
define('DSN', 'mysql://'.DB_USER.':'.DB_PASS.'@'.DB_HOST.'/'.DB_DATABASE);

//接続の生成
$con = MDB::factory(DSN);

$con->open();

$con->query("SET NAMES utf8;");
$result = $con->query("SELECT * FROM test1;");
if(!$result){
  throw new Exception($result->getMessage());
}
$con->close();
print_r($con);
$con->free_connection();
print_r($con);

$data = $result->fetchAll();

#print_r($data);

?>