<?php
class Japanese_Mobile_Emoji_Convert{
	
	/**
	 * 自分自身のインスタンス
	 * @var Japanese_Mobile_Emoji_Convert
	 */
	static $instance;
	
	
	/**
	 * 絵文字番号変換
	 * @var stdClass
	 */
	protected $convert_array;
	
	/**
	 * 絵文字コード
	 * @var stdClass
	 */
	protected $emoji_code;

	
	protected $img_path = "/img/emoji/";
	protected $img_ext = "gif";	
	
	public function singleton($career,$img_path,$img_ext){
		if(!isset(self::$instance)){
			self::$instance = new Japanese_Mobile_Emoji_Convert($career,$img_path,$img_ext);
		}
		return self::$instance;
	}
	
	protected function __construct($career,$img_path = null,$img_ext = null){
		if(!is_null($img_path)){
			if(substr($img_path,-1) != "/"){
				$img_path .= "/";
			}
			$this->img_path = $img_path;
		}
		if(!is_null($img_ext)){
			$this->img_ext = str_replace(array(".","/"),"",$img_ext);
		}
		$this->loadEmojiCode($career);
	}

	/**
	 * 絵文字画像のパスと拡張子を設定する。
	 * @param $emoji_dir_path	絵文字画像のパス
	 * @param $emoji_extntion	絵文字画像の拡張子
	 */
	public function setEmojiImg($emoji_dir_path = null,$emoji_extntion = null){
		if(!is_null($emoji_dir_path)){
			$this->img_path = $emoji_dir_path;
		}
		if(!is_null($emoji_extntion)){
			$this->img_ext = $emoji_extntion;
		}
	}
	
	
	/**
	 * 絵文字コードを読み込む
	 * @return unknown_type
	 */	
	protected function loadEmojiCode($career){
		if(!empty($this->emoji_code)){
			return null;
		}
		//読み込むファイル名を取得する
		$filepath = $this->getLoadEmojiCodeFilename($career);
		//パスがnull以外且つ読み込み可能ならファイルを読み込んでjsonでコードする
		if($career == "pc" && is_readable($filepath)){
			$this->emoji_code = new stdClass();
			$this->emoji_code->img = new stdClass();
			$lines = file($filepath);
			$line_cnt = count($lines);
			for($i=0; $i<$line_cnt; $i++){
				list($num,$name) = explode(",",$lines[$i]);
				$this->emoji_code->img->{$num} = trim($name);
			}
			//pr($this->emoji_code);
		}elseif(!is_null($filepath) && is_readable($filepath)){
			$json = file_get_contents($filepath);
			$this->emoji_code = json_decode($json);
		}else{
			//die("絵文字コードが読み込めません:".$filepath);
		}
	}
	
	
	/**
	 * 出力キャリアにあわせて絵文字コードのファイル名（パス）を返す
	 * @param $career
	 * @return string $path
	 */
	protected function getLoadEmojiCodeFilename($career){
//		if(is_null($career)){
//			$career = $this->outputCareer;
//		}
		$path = dirname(__FILE__)."/emoji/";
		switch($career){
			case "docomo":
				$path .= "docomo_emoji.json";
				break;
			case "au":
				$path .= "ezweb_emoji.json";
				break;
			case "softbank":
				$path .= "softbank_emoji.json";
				break;
			default:
				$path .= "img.csv";
		}
		return $path;
	}
		

	/**
	 * キャリア間での絵文字番号を変換する
	 * @param $emoji_num	絵文字番号
	 * @param $to_career	出力キャリア
	 * @param $from_career	絵文字番号キャリア
	 * @return string		絵文字コード
	 */
	public function doNumberConvert($to_career,$emoji_num_array){
		pr(func_get_args());
		$number = '';
		switch ($to_career){
			case "docomo":
				$number = $this->convert2docomo($emoji_num_array);
				break;
			case "au":
				$number = $this->convert2au($emoji_num_array);
				break;
			case "softbank":
				$number = $this->convert2softbank($emoji_num_array);
				break;
			default:
				if(isset($emoji_num_array["docomo"]) && $emoji_num_array["docomo"]){
					$number = $emoji_num_array["docomo"];
				}elseif(isset($emoji_num_array["au"]) && $emoji_num_array["au"]){
					$number = $this->convert2docomo($emoji_num_array,"au");
				}elseif(isset($emoji_num_array["softbank"]) && $emoji_num_array["softbank"]){
					$number = $this->convert2docomo($emoji_num_array,"softbank");
				}
		}
		return $number;
	}
	
		/**
		 * ドコモ用に変換する
		 * @param $emoji_num	絵文字番号
		 * @param $from_career	絵文字番号キャリア
		 * @return string		絵文字コード
		 */
		protected function convert2docomo($emoji_num_array){
	//		pr($emoji_num_array);
			$emoji_num = 0;
			if(isset($emoji_num_array["docomo"]) && $emoji_num_array["docomo"]){
				$emoji_num = $emoji_num_array["docomo"];
			}else{
				if(isset($emoji_num_array["au"]) && $emoji_num_array["au"]){
					$this->loadConvertFile("ezweb");	//コンバートファイル読み込み
					$emoji_num = $this->convert_array["ezweb"]->{$emoji_num_array["au"]}->docomo;
				}elseif(isset($emoji_num_array["softbank"]) && $emoji_num_array["softbank"]){
					$this->loadConvertFile("softbank");	//コンバートファイル読み込み
					$emoji_num = $this->convert_array["softbank"]->{$emoji_num_array["softbank"]}->docomo;
				}
			}
			return $emoji_num;
		}
		/**
		 * au(ezweb)用に変換する
		 * @param $emoji_num	絵文字番号
		 * @param $from_career	絵文字番号キャリア
		 * @return string		絵文字コード
		 */
		protected function convert2au($emoji_num_array){
			$emoji_num = 0;
			if(isset($emoji_num_array["au"]) && $emoji_num_array["au"]){
				$emoji_num = $emoji_num_array["au"];
			}else{
			
				if(isset($emoji_num_array["docomo"]) && $emoji_num_array["docomo"]){
					$this->loadConvertFile("docomo");	//コンバートファイル読み込み
					$emoji_num = $this->convert_array["docomo"]->{$emoji_num_array["docomo"]}->ezweb;
				}elseif(isset($emoji_num_array["softbank"]) && $emoji_num_array["softbank"]){
					$this->loadConvertFile("softbank");	//コンバートファイル読み込み
					$emoji_num = $this->convert_array["softbank"]->{$emoji_num_array["softbank"]}->ezweb;
				}
			}
			return $emoji_num;
		}
		
		/**
		 * ソフトバンク用に変換する
		 * @param $emoji_num	絵文字番号
		 * @param $from_career	絵文字番号キャリア
		 * @return string		絵文字コード
		 */
		protected function convert2softbank($emoji_num_array){
			$emoji_num = 0;
			if(isset($emoji_num_array["softbank"]) && $emoji_num_array["softbank"]){
				$emoji_num = $emoji_num_array["softbank"];
			}else{
			
				if(isset($emoji_num_array["docomo"]) && $emoji_num_array["docomo"]){
					$this->loadConvertFile("docomo");	//コンバートファイル読み込み
					$emoji_num = $this->convert_array["docomo"]->{$emoji_num_array["docomo"]}->softbank;
				}elseif(isset($emoji_num_array["au"]) && $emoji_num_array["au"]){
					$this->loadConvertFile("ezweb");	//コンバートファイル読み込み
					$emoji_num = $this->convert_array["ezweb"]->{$emoji_num_array["au"]}->softbank;
				}
			}
			return $emoji_num;
		}
		
		
		/**
		 * 絵文字コードのコンバートデータファイルを読み込む
		 * @param $career		読み込むファイルのキャリア名
		 */
		protected function loadConvertFile($career){
			if(!isset($this->convert_array[$career])){
				$filename = dirname(__FILE__)."/emoji/{$career}_convert.json";
				if(file_exists($filename) && is_readable($filename)){
					$json = file_get_contents($filename);
					$obj = json_decode($json);
					$this->convert_array[$career] = $obj->{$career};
				}else{
					die("convert data not found.");
				}
			}		
		}
		
	/**
	 * 絵文字番号を出力キャリア毎に会わせて得文字コードに変換する
	 * @param $to_career
	 * @param $emoji_number
	 * @return unknown_type
	 */
	public function doEmojiConvert($to_career,$emoji_number){
		switch($to_career){
			case "docomo":
				return $this->getEmojiCode4docomo($emoji_number);
				break;
			case "au":
				return $this->getEmojiCode4au($emoji_number);
				break;
			case "softbank":
				return $this->getEmojiCode4softbank($emoji_number);
				break;
			default:
				return $this->getEmojiImgTag($emoji_number);
		}
	}
		/**
		 * 絵文字番号をドコモの絵文字コードに変換する
		 * @return unknown_type
		 */
		protected function getEmojiCode4docomo($emoji_num){
			if($emoji_num){
				$str = "&#x".$this->emoji_code->docomo->$emoji_num->unicode.";";
			return $str;
			}
			
		}
		
		protected function getEmojiCode4au($emoji_num){
//				pr($emoji_num);
//				pr($this->emoji_code->ezweb);
			if($emoji_num){
				$str = "&#x".$this->emoji_code->ezweb->{$emoji_num}->unicode.";";
			return $str;
			}
		}
		
		protected function getEmojiCode4softbank($emoji_num){
//				pr($emoji_num);
//				pr($this->emoji_code->ezweb);
			if($emoji_num){
				$str = "&#x".$this->emoji_code->softbank->{$emoji_num}->unicode.";";
			return $str;
			}
		}

		protected function getEmojiImgTag($emoji_num){
			
			$str = '<img src="'
				.	$this->img_path
				.	$this->emoji_code->img->{$emoji_num}.'.'.$this->img_ext
				.	'" '
				.	'class="emoji emoji_'.$emoji_num.'" />';
			return $str;
		}

}
?>