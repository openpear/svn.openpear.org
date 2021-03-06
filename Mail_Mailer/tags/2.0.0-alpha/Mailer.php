﻿<?php

//内部処理用のエンコードは保留
//mb_internal_encoding('UTF-8');

/*
 * Panasocli Mailer v 2.0.0
 * 2009/01/04
 */

class Mailer
{
	
	//本文が空の場合にはエラーを返す
	public $empty_body_warning = true;
	
	//目的のエンコードを指定する 標準はJIS
	private $target_encode = 'ISO-2022-JP';
	
	//元のエンコードを指定する 標準はmb_convert_encoding準拠のauto
	private $source_encode = 'auto';

	/**
	 *ファイルの存在をチェックする Include_pathも含める
	 * 
	 * @param $file_path ファイルのパス
	 * @return true 成功 false 失敗
	 */	
	private function is_file_ex($file_path){
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
		return false;
	}

	/**
	 *メールサーバに接続する
	 * 
	 * @param $pop3 object PEAR POP3オブジェクト
	 * @param $login array ログイン情報配列
	 * @access public
	 * @return object
	 */	
	private function connectMail($pop3, $config){
		if(!$config->get('login')){
			$config->set('login', 'USER');
		}
		$err = $pop3->connect(
				$config->get('host'), 
				$config->get('port')
			); 
		if(PEAR::isError($err)){
			return $err;
		}
		$err = $pop3->login(
				$config->get('user'), 
				$config->get('password'), 
				$config->get('login')
			); 
		if(PEAR::isError($err)){
			return $err;
		}
		return $pop3;
	}
	
	/**
	 *メールをパースする
	 * 
	 * @param $mail メール本体
	 * @access public
	 * @return array
	 */	
	private function mailParser($mail){
		// メールデータ取得 
		$params['include_bodies'] = true; 
		$params['decode_bodies']  = true; 
		$params['decode_headers'] = true; 
		$params['input'] = $mail; 
		$params['crlf'] = "\r\n"; 
		$structure = Mail_mimeDecode::decode($params); 

		//送信者のメールアドレスを抽出 
		$from = $structure->headers['from']; 
		$from = addslashes($from); 
		$from = str_replace('"','',$from); 

		//署名付きの場合の処理を追加 
		preg_match("/<.*>/",$from,$str); 
		if($str[0]!=""){ 
			$str=substr($str[0],1,strlen($str[0])-2); 
			$from = $str; 
		} 
		$headers = $structure->headers;
		$subject = $headers['subject'];
		//件名だけヘッダーから取り除く 件名は既に変数subjectに入っているので
		unset($headers['subject']);
		
		//fromは加工した奴で上書きする
		$headers['from'] = $from;
		
		switch(strtolower($structure->ctype_primary)){
			case "text": // シングルパート(テキストのみ)  
			//文字コードを変換する
			//charsetから文字コードの検出を試みる
			if(preg_match('/text\/plain/', $headers['content-type'])){
				preg_match_all('/charset="(.+?)"/', $headers['content-type'], $reg);
				$this->source_encode = $reg[1][0] ? $reg[1][0] : 'auto' ;
			}else{
				$this->source_encode = 'auto';
			}
			$body = $this->body ? $this->body : $structure->body ;
			$body = mb_convert_encoding($body, $this->target_encode, $this->source_encode);
			$subject = mb_convert_encoding($subject, $this->target_encode, $this->source_encode);
			break; 
			case "multipart":  // マルチパート 
			foreach($structure->parts as $part){ 
				switch(strtolower($part->ctype_primary)){ 
			  		case "text": // テキスト / HTMLメール
					//内部文字コードに変換する
					//仮にHTMLメールだったらcharsetを確かめる
					if(preg_match('/multipart\/alternative/', $headers['content-type'])){
						$html = explode('<BODY>', $part->body);
						preg_match_all('/charset=(.+?)"/', $html[0], $reg);
						//charsetの値を検出出来なかったらautoにする
						$this->source_encode = $reg[1][0] ? $reg[1][0] : 'auto' ;
					}else{
						$this->source_encode = 'auto';
					}
					$body = mb_convert_encoding($part->body, $this->target_encode, $this->source_encode);
					$subject = mb_convert_encoding($subject, $this->target_encode, $this->source_encode);
				 	break;
					default:  
					$filename[] = $part->ctype_parameters['name'];
					$file[] = base64_encode($part->body);
					break; 
				}
			} 
			break; 
			default: 
			$body = ""; 
		}	
		return array($headers, $subject, $body, $filename, $file);
	}

	/**
	 *メールを受信する
	 * 
	 * @param $user string ユーザ名
	 * @param $password string パスワード
	 * @param $host string ホストアドレス
	 * @param $port int ポート番号
	 * @param $login string ログインモード
	 * @param $delete boolean 受信後に削除するかしないか True=削除 デフォルトはFalse
	 * @access public
	 * @return array
	 */
	public function getMail($config){
		if($config->get('encode')){
			$this->target_encode = $config->get('encode'); 
		}
		if($this->is_file_ex('Net/POP3.php')){
			require_once('Net/POP3.php');
		}else{
			echo 'PEAR::POP3がインストールされていません';
			return false;
		}
		if($this->is_file_ex('Mail/mimeDecode.php')){
			require_once('Mail/mimeDecode.php');
		}else{
			echo 'PEAR::mimeDecodeがインストールされていません';
			return false;
		}
		
		if(!$config->get('user')) {
			echo 'ユーザ名が設定されていません';
			return false;
		}

		if(!$config->get('password')) {
			echo 'パスワードが設定されていません';
			return false;
		}
		
		if(!$config->get('host')) {
			echo 'メールサーバが設定されていません';
			return false;
		}
		
		if(!$config->get('port')) {
			$config->set('port', 110);
		}
		
		$pop3 =new Net_POP3();
		$pop3 =$this->connectMail($pop3, $config);
		if(PEAR::isError($pop3)){
			return $pop3->getMessage();
		}
		$n_msg = $pop3->numMsg(); 
		for($i = 0 ; $i < $n_msg ; $i++){
			list($mail[$i]['headers'], $mail[$i]['subject'], $mail[$i]['body'], $mail[$i]['filename'], $mail[$i]['file']) = $this->mailParser($pop3->getMsg($i + 1));
			//ファイルが添付されていない場合は不要なので配列を消す
			if(empty($mail[$i]['filename'])){
				unset($mail[$i]['filename']);
			}
			if(empty($mail[$i]['file'])){
				unset($mail[$i]['file']);
			}
			if($config->get('search') && !preg_match($config->get('search'), $mail[$i]['subject'])){
				unset($mail[$i]);
			}
			if($config->get('delete') === true){
				$pop3->deleteMsg($i + 1);
			}
		}
		$pop3->disconnect();
		return $mail;
	}

	/**
	 * Smartyを初期化してSmartyオブジェクトを返す
	 * 
	 * @access public
	 * @return 成功 object 失敗 false
	 */		
	private function initSmarty(){
		if($this->is_file_ex('Smarty/Smarty.class.php')){
			require_once('Smarty/Smarty.class.php');
			$smarty = new Smarty();
			$dirs = array(
				'templates',
				'templates_c',
				'configs',
				'cache',
			);
			foreach($dirs as $dir){
				if(!is_dir($dir)){
					mkdir($dir);
					chmod($dir, 0777);
				}
			}
			$smarty->template_dir = 'templates/';
			$smarty->compile_dir  = 'templates_c/';
			$smarty->config_dir   = 'configs/';
			$smarty->cache_dir    = 'cache/';
			return $smarty;
		}else{
			return false;
		}
	}
	
	/**
	 *メールを送信する
	 * 
	 * @param $config object 設定オブジェクト
	 * @access public
	 * @return array
	 */
	public function send($config, $smtp=null){
		if($this->is_file_ex('Mail.php')){
			require_once("Mail.php");
		}else{
			echo 'PEAR::Mailがインストールされていません';
			return false;
		}
		if($this->is_file_ex("Mail/mime.php")){
			require_once("Mail/mime.php");
		}else{
			echo 'PEAR::mimeがインストールされていません';
			return false;
		}
		if(!$config->get('body')){
			if(!$config->get('template')){
				echo 'テンプレートが指定されていません';
				return false;
			}
			if(!is_array($config->get('vars'))){
				echo '値がありません';
				return false;
			}
			$template = $config->get('template');
			$vars = $config->get('vars');
			$smarty = $this->initSmarty();
			if($smarty === false){
				echo 'Smartyがインストールされていません';
				return false;
			}
			foreach($vars as $name => $value){
				$smarty->assign($name, $value);
			}
			$body = $smarty->fetch($template);
		}else{
			$body = $config->get('body');
		}
		
		if(!$body && $this->empty_body_warning === true){
			echo '本文が空です';
			return false;
		}
		
		if(is_null($config->get('from'))){
			$from = 'nobody@localhost';
		}else{
			$from = $config->get('from');
		}
		
		//送信先不明の場合はエラー
		if(!$config->get('mailto')){
			echo '送信先が指定されていません';
			return false;
		}
		
		$subject = $config->get('subject') ? $config->get('subject') : '件名なし' ; 
		
		if($config->get('encode')){
			$this->target_encode = $config->get('encode'); 
		}
		
		$mime = new Mail_Mime("\n");
		if(!is_null($smtp)){
			$mail = Mail::factory("smtp", $smtp);
		}else{
			$mail = Mail::factory("mail");
		}

		$body = mb_convert_encoding($body, $this->target_encode, $this->source_encode);

		$mime->setTxtBody($body);
		
		$attach = $config->get('attach');
		
		if(!is_null($attach)){
			if(is_array($attach)){
				foreach($attach as $val){
					//ファイルが存在するか調べる
					if(!is_file($val)){
						echo sprintf("File Not Found[%s]", $val);
						return false;
					}
					//ファイルが読み取り可能か調べる
					if(!is_readable($val)){
						echo sprintf("File Not Readable[%s]", $val);
						return false;
					}
				}
			}else{
				if(!is_file($attach)){
					echo sprintf("File Not Found[%s]", $attach);
					return false;
				}
				if(!is_readable($attach)){
					echo sprintf("File Not Readable[%s]", $attach);
					return false;
				}
			}
			$mime->addAttachment($attach);
		}

		$body = array(
		  "head_charset" => $this->target_encode,
		  "text_charset" => $this->target_encode
		);

		$body = $mime->get($body);
		if($config->get('bcc') && count($config->get('bcc')) > 1){
			$bcc = implode(',', $config->get('bcc'));	
		}else{
			$bcc = $config->get('bcc');
			$bcc = $bcc[0];
		}

		if($config->get('cc') && count($config->get('cc')) > 1){
			$cc = implode(',', $config->get('cc'));	
		}else{
			$cc = $config->get('cc');
			$cc = $cc[0];
		}

		$header = array(
			"To" => $config->get('mailto'),
			"From" => $from,
			"Bcc" => $bcc,
			"Cc" => $cc,
		  "Subject" => mb_encode_mimeheader(mb_convert_encoding($subject, $this->target_encode, $this->source_encode))
		);

		$header = $mime->headers($header);

		$ret = $mail->send($config->get('mailto'), $header, $body);

		if(PEAR::isError($ret)){
			return $ret->getMessage();
		}
	}
	
	/*
	 * Mailerの設定オブジェクトを返す
	 * return Object
	 */
	public function getMailerConfig(){
		$config = new MailerConfig;
		$config->set('delete', false);
		return $config;
	}
}

class MailerConfig
{
	//設定で使うキー配列
	private $keys = array(
		'mailto',
		'subject',
		'body',
		'from',
		'attach',
		'cc',
		'bcc',
		'search',
		'user',
		'password',
		'host',
		'port',
		'login',
		'delete',
		'encode',
		'template',
		'vars',
	);
	
	/*
	 * キーを設定する
	 * @param $key キー名
	 * @param $val 値
	 */
	public function set($key, $val=null){
		$r = $this->keyCheck($key);
		if($r === false){
			echo '無効なキーです';
			return false;
		}
		if(in_array($key, array('cc', 'bcc'))){
			$this->keys[$key][] = $val;
		}elseif($key == 'search'){
			$this->keys[$key] = '/'.$val.'/';
		}else{
			$this->keys[$key] = $val;
		}
	}
	
	/*
	 * 指定されたキーの値を取得する
	 * @param $key 取得するキー
	 * return string
	 */
	public function get($key){
		return $this->keys[$key];
	}
	
	/*
	 * 正しいキーかチェックする
	 * @param $key 取得するキー
	 * return TRUE:成功 FALSE:失敗
	 */
	private function keyCheck($key){
		if(!in_array($key, $this->keys)){
			return false;
		}
		return true;
	}
	
	/*
	 * 設定情報を配列で返す
	 * return Array
	 */
	public function getArray(){
		foreach($this->keys as $key => $val){
			if(!is_numeric($key)){
				$arr[$key] = $this->get($key);
			}
		}
		return $arr;
	}
}
?>