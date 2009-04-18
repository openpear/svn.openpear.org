<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * デバッグユーティリティ
 * 
 * デバッグ用ユーティリティ関数を定義する
 *
 * Developed on PHP versions 5.1.6
 *
 * @category   Debug
 * @package    
 * @author     quelle <quelle_shake@ybb.ne.jp>
 * @copyright  
 * @license    New BSD Lisence
 * @version    0.1
 * @link       
 * @see        
 * @since      2008/10/26
 * @required   PEAR::Var_Dump
 *
 * HISTORY:
 * 2009/04/18 Created.
 */


/** How to use **/
/*
  It's too easy to use. So you don't need manual.
 */

/****** TODO ******/
/*
  1. Can't output an object with l() function.
  2. Write document.
*/


/*
EX.

 LOG_FILE = "MOGE";
  YYYYMMDD_MOGE.log will create.

*/

define(LOG_FILE, 'hoge');

/**
 * var_dumpエイリアス
 */
function v()
{
//	print '<pre>';
//	var_dump($mixed_var);
//	print '</pre>';
	
	$mixed_var = func_get_args();
	if (1 === count($mixed_var)) {
		$mixed_var = $mixed_var[0];
	}
	
	// PEAR::Var_Dumpを利用
	include_once('Var_Dump.php');
	Var_Dump::displayInit(array('display_mode' => 'HTML4_Table'));
	Var_Dump::display($mixed_var);
}


/**
 * var_dump → exit エイリアス
 */
function ve()
{
	$mixed_var = func_get_args();
	if (1 == count($mixed_var)) {
		$mixed_var = $mixed_var[0];
	}
	v($mixed_var);
	exit;
}


/**
 * debug_backtraceエイリアス
 * @param	int		$param		1:debug_printのみを表示
 */
function d($param = 0)
{
	print '<pre>';
	debug_print_backtrace();
	print '</pre>';
	
	if (1 != $param) {
		v(debug_backtrace());
	}
}


/**
 * ロギング処理エイリアス
 * @param	string		$msg			表示メッセージ
 * @param	string		$log_file		ログファイル名
 */
function l($msg, $log_file = null)
{
	// ログファイルの指定がない場合は定数を利用
	if (!isset($log_file)) {
		if (!defined(LOG_FILE)) {
			$log_file = 'YOU_SHOULD_DEFINE_LOG_FILE.log';
		} else {
			$log_file = LOG_FILE;
		}
	}

	// PEAR::Logを利用
	include_once('Log.php');
	
	// ロガー作成
	$logger =& Log::factory('file', 
		sprintf('%s/%s_%s', dirname($log_file), date('Ymd', time()), basename($log_file)), // logのファイル名を "日付_タイプ.log" に
		sprintf("%s\t%s", $_SERVER['REMOTE_ADDR'], $_SERVER['SCRIPT_NAME']), 
		array('mode'=>0644, 'timeFormat'=>'%Y/%m/%d %H:%M:%S')
	);
	
	// 配列の時はうまいことやる
	$msg = my_out($msg);
	
	// ロギング
	$logger->log($msg, PEAR_LOG_DEBUG);
}

/**
 * 表示してダンプする
 * 
 */
function lv($log_file = null)
{
	$mixed_var = func_get_args();
	if (1 === count($mixed_var)) {
		$mixed_var = $mixed_var[0];
	}

	v($mixed_var);
	l($mixed_var);
}


/**
 * 表示してダンプして終了
 */
function lve($log_file = null)
{
	$mixed_var = func_get_args();
	if (1 === count($mixed_var)) {
		$mixed_var = $mixed_var[0];
	}
	
	lv($log_file);
	exit;
}

/**
 * 配列を平らにする
 *
 * @param	mix		$msg			何でも
 * @return	string					文字列化して返す
 */
function my_out($msg)
{
	if (is_array($msg)) {
		$tmp = 'Array(';
		foreach ($msg as $k => $v) {
			if (is_array($v)) {
				$v = my_out($v);
			}
			$tmp .= sprintf("[%s]=>%s", $k, $v);
			if ($k < count($msg)-1) {
				$tmp .= ', ';
			}
		}
		return $tmp . ')';
	} elseif (is_object($msg)) {
		// not implemented.
	} else {
		return $msg;
	}
}


?>