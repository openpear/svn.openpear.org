<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * デバッグユーティリティクラス
 * 
 * デバッグ用ユーティリティ関数を定義する
 *
 * Developed on PHP versions 5.1.6
 *
 * @category   Utility
 * @package    InfiniteLoop
 * @author     松井 健太郎 <matsui@infiniteloop.co.jp>
 * @copyright  infiniteloop
 * @license    別紙契約内容を参照
 * @version    SVN: $Id: CommonUtilities.php 33 2008-04-04 05:52:40Z matsui $
 * @link       http://www.infiniteloop.co.jp/
 * @see        
 * @since      
 * @deprecated 
 *
 * 修正履歴:
 * 2008/10/25 新規作成
 */

/**
 * var_dumpエイリアス
 */
function v()
{
//	print '<pre>';
//	var_dump($mixed_var);
//	print '</pre>';
	
	$mixed_var = func_get_args();
	if (1 == count($mixed_var)) {
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
		$log_file = LOG_FILE;
	}
	
	// PEAR::Logを利用
	include_once('Log.php');
	
	// ロガー作成
		$logger =& Log::factory('file', 
			sprintf('%s/%s_%s', dirname($log_file), date('Ymd', time()), basename($log_file)), // logのファイル名を "日付_タイプ.log" に
			sprintf("%s\t%s", $_SERVER['REMOTE_ADDR'], $_SERVER['SCRIPT_NAME']), 
			array('mode'=>0644, 'timeFormat'=>'%Y/%m/%d %H:%M:%S')
		);
	
	// ロギング
	$logger->log($msg, PEAR_LOG_DEBUG);
}


?>