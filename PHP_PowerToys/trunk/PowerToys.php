<?php
/**
 * PHP_PowerToys 0.1.0
 * 2009/8/6
 *
 */
class PHP_PowerToys {
	/**
	 * 通常のクラスファクトリー
	 *
	 * @param string $class
	 * @param mixed $arg 配列か文字列か数値かは、インスタンス生成時によって適宜対応
	 * @return object
	 */
	function getInstance($class, $arg=null){
		require_once($class);
		$class = str_replace('.php', '', $class);
		return new $class($arg);
	}
	
	/**
	 * PEARライブラリのクラスファクトリー(インストール風)
	 *
	 * @param string $pear
	 * @param mixed $arg 配列か文字列か数値かは、PEARインスタンス生成時によって適宜対応
	 * @return object
	 */
	function getPearI($pear, $arg=null){
		require_once(str_replace('_', '/', $pear) . '.php');
		return new $pear($arg);
	}
	
	/**
	 * PEARライブラリのクラスファクトリー
	 *
	 * @param string $pear
	 * @param mixed $arg 配列か文字列か数値かは、PEARインスタンス生成時によって適宜対応
	 * @return object
	 */
	function getPear($pear, $arg=null){
		require_once($pear);
		$pear = str_replace('/', '_', $pear);
		$pear = str_replace('.php', '', $pear);
		return new $pear($arg);
	}
	
	/**
	 * 配列の順序を保ったまま、一部のキー名を変更する
	 *
	 * @param array $array
	 * @param string $from
	 * @param string $to
	 * @return Array
	 */
	function arrayKeyReplace($array, $from, $to){
		foreach($array as $key => $val){
			$key = $key == $from ? $to : $key;
			$tmp[$key] = $val;
		}
		unset($array);
		return $tmp;
	}
	
	/**
	 *ファイルの存在をチェックする Include_pathも含める
	 * 
	 * @param $file_path ファイルのパス
	 * @access public
	 * @return true 成功 false 失敗
	 */	
	function is_file_ex($file_path){
		if(is_file($file_path)) return true;

		$include = explode(';', ini_get('include_path'));
		array_shift($include);
		foreach($include as $inc){
			if(is_file($inc . $file_path)){
				return true;
			}
		}
		if(is_file($file_path)){
			return true;
		}

		$include = split(':|;', ini_get('include_path'));
		foreach($include as $inc){
                        if($inc === '.' && is_file($inc . '/' . $file_path)) return true;
			if(is_file($inc . '/' . $file_path)){
				return true;
			}
		}
		if(is_file($file_path)){
			return true;
		}
		return false;
	}
	
	/**
	 * var_dumpの結果を保存する
	 *
	 * @param Array $var
	 * @param string $filename
	 */
	function save_var_dump($var, $filename){
		ob_start();
		var_dump($var);
		$var = ob_get_contents();
		ob_clean();
		$f = fopen($filename, "w");
		fputs($f, $var);
		fclose($f);
	}
	
	/**
	 * 指定されたファイル名もしくは文字列にBOMがあるかチェックする
	 *
	 * @param string $str
	 * @return false BOMなし or UTF-8ではない true BOMつきUTF-8
	 */
	function checkBom($str){
		if(PHP_Powertoys::is_file_ex($str)){
			$str = file_get_contents($str);
		}
		if(mb_detect_encoding($str) != 'UTF-8') return false;
		if (ord($str{0}) == 0xef && ord($str{1}) == 0xbb && ord($str{2}) == 0xbf) {
        	return true;
    	}
    	return false;
	}
	
	/**
	 * 指定されたファイル名もしくは文字列にBOMがあれば除去する
	 *
	 * @param unknown_type $str
	 * @return unknown
	 */
	function removeBom($str){
		if(PHP_Powertoys::is_file_ex($str)){
			$str = file_get_contents($str);
		}
		if(mb_detect_encoding($str) != 'UTF-8') return false;
		if (ord($str{0}) == 0xef && ord($str{1}) == 0xbb && ord($str{2}) == 0xbf) {
        		$str = substr($str, 3);
    	}
    	return $str;
	}
	
	/**
	 * 画像を自動認識してGDのリソース情報を返す
	 *
	 * @param string $img 画像フルパスにて
	 * @return 成功:GD Resource 失敗:False
	 */
	function iopen($img){
		//GDがインストールされているかチェックする
		$extension = "gd"; 
		$extension_soname = $extension . "." . PHP_SHLIB_SUFFIX; 
		$extension_fullname = PHP_EXTENSION_DIR . "/" . $extension_soname;
		if(!extension_loaded($extension)) { 
		    return false; 
		} 
		$content = file_get_contents($img);
		if ( preg_match( '/^\x89PNG\x0d\x0a\x1a\x0a/', $content) )  {
			$gd = imagecreatefrompng($img);
		} elseif ( preg_match( '/^GIF8[79]a/', $content) )  {
			$gd = imagecreatefromgif($img);
		} elseif ( preg_match( '/^\xff\xd8/', $content) )  {
			$gd = imagecreatefromjpeg($img);
		}else{
			return false;
		}
		return $gd;
	}
	
	/**
	 * print_rの出力結果をブラウザ上からでもわかりやすくする
	 *
	 * @param Array $var
	 */
	function print_r_ex($var){
		$tmp = print_r($var, true);
		$tmp = preg_replace("/ /", "&nbsp;", $tmp);
		$tmp = nl2br($tmp);
		$tmp = mb_convert_encoding($tmp, mb_internal_encoding());
		echo $tmp;
	}
	
	/**
	 * オブジェクトの完全ディープコピーをとる、PHP5のcloneは深いオブジェクトを見に行けない
	 *
	 * @param object $obj
	 * @return 失敗時:False 成功時:object
	 */
	function objectClone($obj){
		if(!is_object($obj)) return false;
		return unserialize(serialize($obj));
	}
	
	/**
	 * allow_url_fopenがoffでもURLのデータを取得してくる
	 *
	 * @param string $url
	 * @return 失敗時:False 成功時:データ
	 */
	function file_get_contents_ex($url){
		$client = PHP_Powertoys::getPearI('HTTP_Client');
		$client->get($url);
		$response = $client->currentResponse();
		if($response['code'] == 200){
			return $response['body'];
		}
		return false;
	}
}
?>