<?php
require_once(dirname(__FILE__)."/Japanese_Mobile_Emoji_Convert.php");
class Japanese_Mobile_Emoji{
	
	/**
	 * 自分自身のインスタンス
	 * @var Japanese_Mobile_Emoji
	 */
	static $instance;
	
	/**
	 * @var JpMobileEmojiConvert
	 */
	protected $emojiConvert;
	
	protected $outputCareer = "pc";
	
	
	/**
	 * 
	 * @var Net_UserAgent_Mobile
	 */
	protected $mobile;
	
	protected $options = array(	
		"emoji_dir_path" => "/img/emoji/",	//絵文字画像のパス
		"emoji_ext" => "gif",				//絵文字画像の拡張子
	);
	
	/**
	 * インスタンスを返すメソッド
	 * @return EmojiTest
	 */
	public function singleton(){
		if(!isset(self::$instance)){
			self::$instance = new Japanese_Mobile_Emoji();
		}
		return self::$instance;
	}
	
	/**
	 * 初期設定
	 * @return unknown_type
	 */
	protected function __construct(){
		$this->mobile = Net_UserAgent_Mobile::singleton();
		
		//出力キャリアを設定する
		switch(true){
			case $this->mobile->isDoCoMo():
				$this->outputCareer = "docomo";
				break;
			case $this->mobile->isEZweb():
				$this->outputCareer = "au";
				break;
			case $this->mobile->isSoftBank():
				$this->outputCareer = "softbank";
				break;
			
			default:
				$this->outputCareer = "pc";
		}

		//絵文字コンバート用のクラスのインスタンスを作成	
		$this->emojiConvert = JpMobileEmojiConvert::singleton(
															$this->outputCareer,
															$this->options["emoji_dir_path"],
															$this->options["emoji_ext"]
															);
		//絵文字コードが読み込まれていない場合は読み込む
//		if(empty($this->emojiCode)){
//			$this->emojiConvert->loadEmojiCode();
			
//		}
	}

	
	protected function emoji_output_change($matches){
		//入力データが無い場合
		if(empty($matches) || !is_array($matches) || count($matches) != 2){
			return '';
		}
		//入力データを取得する
		$emoji_num_csv = trim($matches[1]);
		if(empty($emoji_num_csv)){
			return '';
		}
		//変換する文字が無い場合はここまでで終了
		
		//絵文字指定文字列を分割する
		$emoji_num_array = array();
		list($emoji_num_array["docomo"],$emoji_num_array["au"],$emoji_num_array["softbank"]) 
			= 	explode(",", $emoji_num_csv);
			
		if(in_array($this->outputCareer,array("docomo","au","softbank"))){
			$convert_to = $this->outputCareer;
		}else{
			$convert_to = "img";
		}
		$emoji_numer = $this->emojiConvert->doNumberConvert($convert_to,$emoji_num_array);
		return $this->emojiConvert->doEmojiConvert($convert_to,$emoji_numer);
/*								
		switch($this->outputCareer){
			case "docomo":
//				pr($this->emojiCode->docomo->$emoji_num);
//				return "&#x".$this->emojiCode->docomo->$emoji_num->unicode.";";
				
				break;
			case "au":
				break;
			case "softbank":
				break;
			default:
				break;
		}
*/		
	}
	
	public function doConvert2output($bf){
		$bf = preg_replace_callback(
				 		'/\[emoji:([0-9:]+)\]/',
						array($this,'emoji_output_change'),
						$bf
					);	
		return $bf;		
	}
	
	/**
	 * 絵文字画像のパスと拡張子を設定する。変更する場合はdoConvert2outputの前にコールすること。
	 * @param $emoji_dir_path	絵文字画像のパス
	 * @param $emoji_extntion	絵文字画像の拡張子
	 */
	public function setEmojiImg($emoji_dir_path = null,$emoji_extntion = null){
		$this->emojiConvert->setEmojiImg($emoji_dir_path,$emoji_extntion);
	}
	
	/**
	 * このインスタンスの複製を許可しないようにする
	 * @throws RuntimeException
	 */
	public final function __clone(){
		throw new RuntimeException('Clone is not allowd against '.get_class($this));
	}
	
	
}
?>