<?php
/**
 * PHP_MakeInfo v 1.0 Beta
 *
 * @author FreeBSE
 */

class MakeInfo{

	/**
	 * phpinfo()ライクなページを生成する
	 * 
	 * @param $name string ページ名
	 * @param $title string 情報タイトル(テーブルのキャプション)
	 * @param $contents array 表示する情報
	 * @access public
	 */
	public function __construct($name, $title, $contents){
		if(!isset($name)){
			$data['name'] = "NoName";
		}
		if(!isset($title)){
			$data['title'] = "NoTitle";
		}
		if(!$this->varValidate($contents)){
			echo "ERROR! Invalid variable.";
			exit;
		}
		$data['name'] = $name;
		$data['title'] = $title;
		foreach($contents as $key => $val){
			$c .= "<tr><td class=\"e\">{$key}</td><td class=\"v\">{$val}</td></tr>";
		}
		$data['contents'] = $c;
		$file = file_get_contents(dirname(__FILE__) . "/template.tpl");
		foreach($data as $key => $val){
		    $file = str_replace("[{$key}]", $val, $file);
		}		
		echo $file;
		//ここではinfoを表示するだけなので余計な処理をさせないように処理の強制終了
		exit;
	}
	
	/**
	 * 変数の中身を検証する
	 * 
	 * @param $name array $vars
	 * @access private
	 */
	private function varValidate($vars){
		if(!is_array($vars)){
			return false;
		}
		foreach($vars as $key => $val){
			if(!isset($key)){
				return false;
			}
			if(!isset($val)){
				return false;
			}
		}
		return true;
	}
}
?>