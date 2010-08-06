<?php
/**
 *  PHP_PowerToys 0.2.9
 *
 *  @author	    FreeBSE <freebse@live.jp> <http://panasocli.cc/wordpress>
 *  @package	PHP_PowerToys
 *  @version	PHP_PowerToys v 0.2.9
 * 
 */
class PHP_PowerToys {
	
	function PHP_PowerToys($option=null){
		if($option==='compress') {
		    require_once 'Net/UserAgent/Mobile.php';
		    $mobile =& Net_UserAgent_Mobile::factory();
		    if (!$mobile->isDocomo() || !$mobile->isSoftbank() || !$mobile->isEZWeb()) return false;
		    ob_start();
		}
	}
	
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
		require_once(strtr('_', '/', $pear) . '.php');
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
		$pear = strtr('/', '_', $pear);
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

		$include = split(':|;', ini_get('include_path'));
		foreach($include as $inc){
			if($inc === '.' && is_file($inc . '/' . $file_path)) return true;
			if(is_file($inc . '/' . $file_path)){
				return true;
			}
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
		$v = ob_get_contents();
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
		if(mb_detect_encoding($str) !== 'UTF-8') return false;
		if (ord($str{0}) == 0xef && ord($str{1}) == 0xbb && ord($str{2}) == 0xbf) {
        	return true;
    	}
    	return false;
	}
	
	/**
	 * 指定されたファイル名もしくは文字列にBOMがあれば除去する
	 *
	 * @param string $str
	 * @return string
	 */
	function removeBom($str){
	    if(PHP_Powertoys::is_file_ex($str)){
		$str = file_get_contents($str);
	    }
	    if(mb_detect_encoding($str) !== 'UTF-8') return false;
	    if (ord($str{0}) == 0xef && ord($str{1}) == 0xbb && ord($str{2}) == 0xbf) {
		    $str = substr($str, 3);
	    }
	    return $str;
	}
	
	/**
	 * 指定されたファイル名もしくは文字列にBOMがなければ付加する(うわぁぁぁ死ねばいいのに・・・)
	 *
	 * @param string $str
	 * @return string
	 */
	function addBom($str){
	    if(PHP_Powertoys::is_file_ex($str)){
		$str = file_get_contents($str);
	    }
	    if(mb_detect_encoding($str) != 'UTF-8') return false;
	    if (ord($str{0}) != 0xef && ord($str{1}) != 0xbb && ord($str{2}) != 0xbf) {
		$str = chr(0xef) . chr(0xbb) . chr(0xbf) . $str;
	    }
	    return $str;
	}
	
	/**
	 * 拡張(PHPエクステンション)があるかチェックする
	 *
	 * @param string $extension
	 * @return 成功:TRUE 失敗:FALSE
	 */
	function extensionExist($extension){
	    $extension_soname = $extension . '.' . PHP_SHLIB_SUFFIX;
	    $extension_fullname = PHP_EXTENSION_DIR . '/' . $extension_soname;
	    if(!extension_loaded($extension) && !is_file($extension_fullname)) {
		return false;
	    }
	    return true;
	}
	
	/**
	 * 画像を自動認識してGDのリソース情報を返す
	 *
	 * @param string $img 画像フルパスにて
	 * @return 成功:GD Resource 失敗:False
	 */
	function iopen($img){
		//GDがインストールされているかチェックする
		 if(!PHP_PowerToys::extensionExist('gd')) return false;
		$content = file_get_contents($img);
		if ( preg_match( '/^\x89PNG\x0d\x0a\x1a\x0a/', $content) )  {
			$gd = imagecreatefrompng($img);
		} elseif ( preg_match( '/^GIF8[79]a/', $content) )  {
			$gd = imagecreatefromgif($img);
		} elseif ( preg_match( '/^\xff\xd8/', $content) )  {
			$gd = imagecreatefromjpeg($img);
		} elseif ( preg_match( '/^BM|^\x42\x4d/', $content) )  {
			$gd = PHP_PowerToys::ImageCreateFromBMP($img);
		}else{
			return false;
		}
		return $gd;
	}
	
	//from http://jp.php.net/manual/ja/function.imagecreate.php#53879
	/*********************************************/
	/* Fonction: ImageCreateFromBMP              */
	/* Author:   DHKold                          */
	/* Contact:  admin@dhkold.com                */
	/* Date:     The 15th of June 2005           */
	/* Version:  2.0B                            */
	/*********************************************/
	function ImageCreateFromBMP($filename)
	{
	 //Ouverture du fichier en mode binaire
	   if (! $f1 = fopen($filename,"rb")) return FALSE;
	
	 //1 : Chargement des ent�tes FICHIER
	   $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
	   if ($FILE['file_type'] != 19778) return FALSE;
	
	 //2 : Chargement des ent�tes BMP
	   $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
	                 '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
	                 '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
	   $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
	   if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
	   $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
	   $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
	   $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
	   $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
	   $BMP['decal'] = 4-(4*$BMP['decal']);
	   if ($BMP['decal'] == 4) $BMP['decal'] = 0;
	
	 //3 : Chargement des couleurs de la palette
	   $PALETTE = array();
	   if ($BMP['colors'] < 16777216)
	   {
	    $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
	   }
	
	 //4 : Cr�ation de l'image
	   $IMG = fread($f1,$BMP['size_bitmap']);
	   $VIDE = chr(0);
	
	   $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
	   $P = 0;
	   $Y = $BMP['height']-1;
	   while ($Y >= 0)
	   {
	    $X=0;
	    while ($X < $BMP['width'])
	    {
	     if ($BMP['bits_per_pixel'] == 24)
	        $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
	     elseif ($BMP['bits_per_pixel'] == 16)
	     { 
	        $COLOR = unpack("n",substr($IMG,$P,2));
	        $COLOR[1] = $PALETTE[$COLOR[1]+1];
	     }
	     elseif ($BMP['bits_per_pixel'] == 8)
	     { 
	        $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
	        $COLOR[1] = $PALETTE[$COLOR[1]+1];
	     }
	     elseif ($BMP['bits_per_pixel'] == 4)
	     {
	        $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
	        if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
	        $COLOR[1] = $PALETTE[$COLOR[1]+1];
	     }
	     elseif ($BMP['bits_per_pixel'] == 1)
	     {
	        $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
	        if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
	        elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
	        elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
	        elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
	        elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
	        elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
	        elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
	        elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
	        $COLOR[1] = $PALETTE[$COLOR[1]+1];
	     }
	     else
	        return FALSE;
	     imagesetpixel($res,$X,$Y,$COLOR[1]);
	     $X++;
	     $P += $BMP['bytes_per_pixel'];
			}
		$Y--;
		$P+=$BMP['decal'];
		}

	//Fermeture du fichier
	fclose($f1);

	return $res;
	}
	
	/**
	 * BMPで表示および保存 > http://php.benscom.com/manual/ja/ref.image.php
	 *
	 * @param GDリソース $im
	 * @param string $fn
	 */
	function imagebmp ($im, $fn = false)
	{
	    if (!$im) return false;
	           
	    if ($fn === false) $fn = 'php://output';
	    $f = fopen ($fn, "w");
	    if (!$f) return false;
	           
	    //Image dimensions
	    $biWidth = imagesx ($im);
	    $biHeight = imagesy ($im);
	    $biBPLine = $biWidth * 3;
	    $biStride = ($biBPLine + 3) & ~3;
	    $biSizeImage = $biStride * $biHeight;
	    $bfOffBits = 54;
	    $bfSize = $bfOffBits + $biSizeImage;
	           
	    //BITMAPFILEHEADER
	    fwrite ($f, 'BM', 2);
	    fwrite ($f, pack ('VvvV', $bfSize, 0, 0, $bfOffBits));
	           
	    //BITMAPINFO (BITMAPINFOHEADER)
	    fwrite ($f, pack ('VVVvvVVVVVV', 40, $biWidth, $biHeight, 1, 24, 0, $biSizeImage, 0, 0, 0, 0));
	           
	    $numpad = $biStride - $biBPLine;
	    for ($y = $biHeight - 1; $y >= 0; --$y)
	    {
	        for ($x = 0; $x < $biWidth; ++$x)
	        {
	            $col = imagecolorat ($im, $x, $y);
	            fwrite ($f, pack ('V', $col), 3);
	        }
	        for ($i = 0; $i < $numpad; ++$i)
	            fwrite ($f, pack ('C', 0));
	    }
	    fclose ($f);
	    return true;
	}
	
	/**
	 * imlib2拡張を使用したリサイズ(高速らしい)
	 *
	 * @param string $input
	 * @param string $output
	 * @param int $width
	 * @param int $height
	 */
	function imlibResizer($input, $output, $width, $height){
		if(!PHP_PowerToys::extensionExist('imlib2')) return false;
		$f = imlib2_load_image($input);
		imlib2_create_scaled_image($f, $width, $height);
		imlib2_save_image($f, $output);
	}
	
	/**
	 * print_rの出力結果をブラウザ上からでもわかりやすくする
	 *
	 * @param Array $var
	 */
	function print_r_ex($var){
		$tmp = print_r($var, true);
		$tmp = preg_replace('/ /', '&nbsp;', $tmp);
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
	
	/**
	 * in_arrayを拡張して、正規表現も使えるようにします
	 *
	 * @param string $target
	 * @param array $array
	 * @return 検出カウントint  なし:FALSE
	 */
	function in_array_ex($target, $array){
		$j = intval(0);
		for($i = 0, $n = count($array) ; $i < $n ; $i++){
			if(preg_match('/'.$target.'/', $array[$i])){
				$j++;
			}
		}
		return $j === 0 ? false : $j ; 
	}
	
	/**
	 * iniファイルを処理できます
	 * 
	 * @param  string $str
	 * @return array
	 */
	function iniParser($str){
		if(PHP_Powertoys::is_file_ex($str)){
			$str = file_get_contents($str);
		}
		$str = preg_split('/\r|\n|\r\n/', $str);
		foreach($str as $val){
			if(!$val) continue;
			list($name, $value) = preg_split('/( |	)*=( |	)*/', $val);
			$ini[preg_replace('/( |	)*=( |	)*/', '', $name)] = preg_replace('/( |	)*=( |	)*/', '', $value); 
		}
		return $ini;
	}
	
	/**
	 * ファイルを16進数で表示します(嗚呼、コンピュータって感じです)
	 *
	 * @param string $str
	 */
	function hexFromFile($str){
		if(!PHP_Powertoys::is_file_ex($str)) return false;
		$str = file_get_contents($str);		
		for($i = 0, $n = strlen($str) ; $i <= $n ; $i++){
			$hex[] = strtoupper(dechex(ord($str[$i])));
		}
		$hex = implode(' ', $hex);
		return $hex;
	}
	
	/**
	 * ファイルを2進数で表示します(嗚呼、よりコンピュータって感じです)
	 *
	 * @param string $str
	 */
	function binFromFile($str){
		if(!PHP_Powertoys::is_file_ex($str)) return false;
		$str = file_get_contents($str);		
		for($i = 0, $n = strlen($str) ; $i <= $n ; $i++){
			$bin[] = decbin(ord($str[$i]));
		}
		$bin = implode(' ', $bin);
		return $bin;
	}
	
	/**
	 * メモリ開放を行う
	 * PHP5.3からはガベージコレクタが追加されたので意味がなくなる
	 *
	 * @param int $debug メモリ使用量の参照
	 */
	function garbageCollection($debug=false){
		if($debug && preg_match('/^5\./', phpversion())){
			echo memory_get_usage(),'<br />';
		}
		unset($GLOBALS);
		foreach(get_defined_vars() as $key => $val){
			unset($key);
			unset($val);
		}
		unset($this);
		if($debug && preg_match('/^5\./', phpversion())){
			unset($debug);
			echo memory_get_usage(),'<br />';
		}
	}
		
	/**
	 * HTMLの不要な部分を取り除いて圧縮する(携帯向け)
	 * 携帯ではdeflateが効かないので主に携帯向け、デザイン構造が見られるわけでもないし
	 * 最近では携帯も高速化されパケット節約の必要もなくなったが、より表示速度が速くなる事に越した事はない
	 *
	 */
	function compressMobileHtml($option=null){
		require_once 'Net/UserAgent/Mobile.php';
		$mobile =& Net_UserAgent_Mobile::factory();
		if (!$mobile->isDocomo() || !$mobile->isSoftbank() || !$mobile->isEZWeb()) return false;
		$f = ob_get_contents();
		ob_clean();
		$before = strlen($f);
		if($option == 'debug'){
			echo 'Before:',strlen($f),'Bytes<br>';
		}
		$f = preg_replace('/\r\n|\r|\n|\t|	/', '', $f);
		$f = preg_replace("/[a-zA-Z0-9]+=\"{2}?/", "", $f);
		$f = str_replace("&nbsp;", " ", $f);
		$f = str_replace("&amp;", "&", $f);
		$f = trim($f);
		$f = trim($f,"\x00..\x1F");
		$f = preg_replace('/id=\"[a-zA-Z0-9]+\" /', '', $f);
		$f = preg_replace('/style=\"[a-zA-Z0-9]+\" /', "", $f);
		preg_match_all("/(<!--.+?-->)/ms", $f, $reg);
		foreach($reg[0] as $val){
		    $f = str_replace($val, "", $f);
		}

		$cvt_from = array('<br />','<strong>','<\/strong>','<em>','</em>','<strike>','</strike>');
		$cvt_to = array('<br>','<b>','</b>','<i>','</i>','<s>','</s>');
		foreach($cvt_from as $key => $val){
		    $f = str_replace($cvt_from[$key], $cvt_to[$key], $f);
		}

		$f = mb_convert_kana($f, 'as');
		$per = (int) (strlen($f) / $before * 100);
		if($option == 'debug'){
			echo 'After:',strlen($f),'Bytes<br>Compressibility:',$per,'%';
		}
		echo $f;
	}

	/**
	 * 配列をオブジェクト(DTO)にして返す sample : $object->get
	 * 但し、多次元配列の場合はgetで取れた値も配列のまま
	 *
	 * @param Array $array
	 * @return Object
	 */
	function arrayToDto($array){
	    $dto = & new DTO();
	    foreach($array as $key => $val){
		$dto->$key = $val;
	    }
	    return $dto;
	}

	/**
	 * PHP_PowerToysでオブジェクト化(DTO)したのを配列に変換する
	 *
	 * @param Object $dto
	 * @return Array
	 */
	function dtoToArray($dto){
	    $array = array();
	    foreach($dto as $key => $val){
		$array[$key] = $val;
	    }
	    return $array;
	}

	/**
	 *
	 * 多次元配列の最多次元数を返す
	 *
	 * @param Array $array
	 * @return int
	 */
	function getCountDimentionArrays($array){
	    if(!is_array($array)) return false;
	    foreach($array as $val){
		if(is_array($val)){
		    global $cnt;
		    $cnt++;
		    $this->getCountDimentionArrays($val);
		}
	    }
	    return $cnt++;
	}

	/**
	 *
	 * PHP5のfile_put_contents関数をPHP4向けに提供
	 *
	 * @param string $filename
	 * @param string $data
	 * @param string $mode
	 * @return TRUE:成功 FALSE:失敗
	 */
	function file_put_contents($filename, $data, $mode='w'){
	    $f = fopen($filename, $mode);
	    fputs($f, $data);
	    fclose($f);
	    if(!file_exists($filename)){
		return false;
	    }
	    return true;
	}

	/**
	 * PHP5のscandir関数でカレントディレクトリを示す「.」や親ディレクトリを示す「..」を除去して返す
	 * PHP4も一応サポート
	 *
	 * @param String $dir
	 * @param int $options
	 * @param int $ignore_parent
	 * @return $dirs Array
	 */
	function scandir_ex($dir, $options=0, $ignore_parent=false){
	    if (version_compare(PHP_VERSION, '5.0.0', '<')) {
		if ($handle = opendir($dir)) {
		    while (false !== ($file = readdir($handle))) {
		    if($ignore_parent === true && $file !== '.' || $file !== '..'){
			$dirs[] = $val;
		    }
		}
		return $dirs;
	    }
	    $tmp = scandir($dir, $options);
	    foreach($tmp as $val){
		if($ignore_parent === true && $val !== '.' || $val !== '..'){
		    $dirs[] = $val;
		}
	    }
	    return $dirs;
	}
    }

    /**
     * 指定した数字で文字列を区切って配列で返す
     *
     * @param String $str
     * @param int $num
     * @return Array
     */
    function numSplit($str, $num){
	for($i = 1, $n = strlen($str); $i <= $n ; $i++){
		$tmp .= substr($str, $i -1, 1);
		if($i % $num === 0){
			$splits[] = $tmp;
			$tmp = "";
		}
	}
	return $splits;
    }
    
    /**
     * strip_tagsの拡張版(strip_tagsで対応出来ないタグやNULLバイトを除去する)
     * 
     * @param String $str
     * @param String $tag
     * @return String
     */
    function strip_tags_ex($str, $tag=null){
	$str = strip_tags($tag);
	$str = is_null($tag) ? htmlspecialchars($str, ENT_QUOTES) : $str ;
	$str = trim($str);
	$str = preg_replace("/\/x00|\/00/", "", $str);
	return $str;
    }
}

class DTO {
	function get($key){
	    return $this->$key;
	}
}
?>