<?php
/**
 *  Mailer.php
 *
 *  @author     FreeBSE <freebse@live.jp>
 *  @package    Mail_Mailer
 *  @version    Mailer.php v 2.0.0 2009/08/27
 * 
 */

interface Mailer {
	public function getMail();
	public function send();
	public function set($key, $val=null);
	public function get($key);
}

class Mail_Mailer implements Mailer 
{
	
	//本文が空の場合にはエラーを返す
	public $empty_body_warning = true;
	
	//目的のエンコードを指定する 標準はJIS
	private $target_encode = 'ISO-2022-JP';
	
	//元のエンコードを指定する 標準はmb_convert_encoding準拠のauto
	private $source_encode = 'auto';
	
        /**
         *エラーメッセージ処理の統一
         *
         * @param string $str
         */
        final public function showError($str){
            echo mb_detect_encoding($str) !== mb_internal_encoding() ?
                    mb_convert_encoding($str, mb_internal_encoding(), 'auto') :
                    $str ;
        }

        /**
         *通常メッセージ処理の統一
         *
         * @param string $str
         */
        final public function showNotice($str){
            echo mb_detect_encoding($str) !== mb_internal_encoding() ?
                    mb_convert_encoding($str, mb_internal_encoding(), 'auto') :
                    $str ;
        }

	/**
	 *ファイルの存在をチェックする Include_pathも含める
	 * 
	 * @param $file_path ファイルのパス
	 * @access public
	 * @return true 成功 false 失敗
	 */	
	final public function is_file_ex($file_path){
		if(is_file($file_path)) return true;
		//こちらでも動くので互換性のために一応残す
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
		//修復版
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
	 * PEARライブラリのクラスファクトリー
	 *
	 * @param string $pear
	 * @param mixed $arg 配列か文字列か数値かは、PEARインスタンス生成時によって適宜対応
	 * @return object
	 */
	final protected function getPear($pear, $arg=null){
		require_once(str_replace('_', '/', $pear) . '.php');
		return new $pear($arg);
	}

	/**
	 *メールサーバに接続する
	 * 
	 * @param $pop3 object PEAR POP3オブジェクト
	 * @access private
	 * @return object
	 */	
	private function connectMail($pop3){
		if(!$this->get('login')){
			$this->set('login', 'USER');
		}
		$err = $pop3->connect(
				$this->get('host'), 
				$this->get('port')
			); 
		if(PEAR::isError($err)){
			return $err;
		}
		$err = $pop3->login(
				$this->get('user'), 
				$this->get('password'), 
				$this->get('login')
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
	 * @access private
	 * @return array
	 */	
	private function mailParser($mail){
		// メールデータ取得 
		$params['include_bodies'] = true; 
		$params['decode_bodies']  = true; 
		$params['decode_headers'] = true;  
		$params['crlf'] = "\r\n"; 
		//TODO ここら辺を先に重点的に見直す必要あり
		if($this->is_file_ex('Mail/mimeDecode.php')){
			$mime = $this->getPear('Mail_mimeDecode', $mail);
		}else{
			$this->showError('PEAR::Mail_mimeDecodeがインストールされていません');
			return false;
		}
		$structure = $mime->decode($params);

		//送信者のメールアドレスを抽出 
		$from = $structure->headers['from']; 
		$from = addslashes($from); 
		$from = str_replace('"','',$from); 
		$name = stripslashes($from);

		//署名付きの場合の処理を追加 
		preg_match("/<.*>/",$from,$str); 
		if($str[0]!=""){ 
			$from=substr($str[0],1,strlen($str[0])-2); 
		} 
		$headers = $structure->headers;
		$subject = $headers['subject'];
		//件名だけヘッダーから取り除く 件名は既に変数subjectに入っているので
		unset($headers['subject']);
		
		//fromは加工した奴で上書きする
		$headers['from'] = $from;
		//差出人上書き
		$headers['name'] = $name;
		
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
	 * 受信設定が有効かをチェックする
	 *
	 * @return 成功:TRUE 失敗:FALSE
	 */
	private function validateGetConfig(){
		if(!$this->get('user')) {
			$this->showError('ユーザ名が設定されていません');
			return false;
		}

		if(!$this->get('password')) {
			$this->showError('パスワードが設定されていません');
			return false;
		}
		
		if(!$this->get('host')) {
			$this->showError('メールサーバが設定されていません');
			return false;
		}
		
		if(!$this->get('port')) {
			$this->set('port', 110);
		}
		return true;
	}

	/**
	 *メールを受信する
	 * 
	 * @access public
	 * @return array
	 */
	public function getMail(){
		if($this->validdateGetConfig() === false) return false;
		if($this->get('encode')){
			$this->target_encode = $this->get('encode'); 
		}
		if($this->is_file_ex('Net/POP3.php')){
			$pop3 = $this->getPear('Net_POP3');
		}else{
			$this->showError('PEAR::Net_POP3がインストールされていません');
			return false;
		}
		$pop3 =$this->connectMail($pop3);
		if(PEAR::isError($pop3)){
			return $pop3->getMessage();
		}
		$n_msg = $pop3->numMsg();
		for($i = 0 ; $i < $n_msg ; $i++){
			list($mail[$i]['headers'], $mail[$i]['subject'], $mail[$i]['body'], $mail[$i]['filename'], $mail[$i]['file']) = $this->mailParser($pop3->getMsg($i + 1));
			$mail[$i]['id'] = $i + 1;
			//ファイルが添付されていない場合は不要なので配列とオブジェクトを消す
			if(empty($mail[$i]['filename'])){
				unset($mail[$i]['filename']);
			}
			if(empty($mail[$i]['file'])){
				unset($mail[$i]['file']);
			}
			if($this->get('search') && !preg_match($this->get('search'), $mail[$i]['subject'])){
				unset($mail[$i]);
			}
			if($this->get('smarty') === false){
				$mail = $this->arrayToObject($mail);
			}
			if($this->get('delete') === true){
				$pop3->deleteMsg($i + 1);
			}
		}
		$pop3->disconnect();
		//不要なオブジェクトは破棄する
		unset($pop3);
		return $mail;
	}
	
	/**
	 * メール配列を擬似OR/M風に改造する
	 *
	 * @param Array $mail
	 * @return Object
	 */
	private function arrayToObject($mail){
		foreach($mail as $key => $val){
			foreach($val as $k => $v){
				if(in_array($k, $this->keys))
				$this->set($k, $v);
			}
			$mail_obj[$key] = clone $this;
		}
		//不要になった配列は破棄する
		unset($mail);
		return $mail_obj;
	}
	
	/**
	 * メッセージの削除を行う
	 *
	 * @return unknown
	 */
	function deleteMsg(){
		if($this->validConfig() === false) return false;
		if($this->is_file_ex('Net/POP3.php')){
			$pop3 = $this->getPear('Net_POP3');
		}else{
			$this->showError('PEAR::Net_POP3がインストールされていません');
			return false;
		}
		$pop3 =$this->connectMail($pop3);
		if(PEAR::isError($pop3)){
			return $pop3->getMessage();
		}
		foreach($this->get('deleteMsg') as $val){
			$pop3->deleteMsg($val);
		}
		$pop3->disconnect();
		unset($pop3);
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
				dirname(__FILE__) . '/templates',
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
			$smarty->template_dir = dirname(__FILE__) . '/templates/';
			$smarty->compile_dir  = 'templates_c/';
			$smarty->config_dir   = 'configs/';
			$smarty->cache_dir    = 'cache/';
			return $smarty;
		}else{
			return false;
		}
	}
	
	/**
	 * 送信設定が有効かをチェックする
	 *
	 * @return 成功:TRUE 失敗:FALSE
	 */
	private function validateSendConfig(){
		if(!$this->get('body')){
			if(!$this->get('template')){
				$this->showError('テンプレートが指定されていません');
				return false;
			}
			if(!is_array($this->get('vars'))){
				$this->showError('値がありません');
				return false;
			}
			$smarty = $this->initSmarty();
			if($smarty === false){
				$this->showError('Smartyがインストールされていません');
				return false;
			}
			foreach($this->get('vars') as $name => $value){
				$smarty->assign($name, $value);
			}
			$this->set('body', $smarty->fetch($this->get('template')));
		}
		if(!$this->get('body') && $this->empty_body_warning === true){
			$this->showNotice('本文が空です');
			return false;
		}
		//送信先不明の場合はエラー
		if(!$this->get('mailto')){
			$this->showError('送信先が指定されていません');
			return false;
		}
		
		if(!is_null($this->get('attach'))){
			if(is_array($this->get('attach'))){
				foreach($this->get('attach') as $val){
					//ファイルが存在するか調べる
					$val = strpos(PHP_OS, 'WIN') !== false ? mb_convert_encoding($val, 'SJIS', mb_internal_encoding()) : mb_convert_encoding($val, 'EUC', mb_internal_encoding()) ;
					if(!is_file($val)){
						$this->showError(sprintf("File Not Found[%s]", $val));
						return false;
					}
					//ファイルが読み取り可能か調べる
					if(!is_readable($val)){
						$this->showError(sprintf("File Not Readable[%s]", $val));
						return false;
					}
				}
			}else{
				$attach = strpos(PHP_OS, 'WIN') !== false ? mb_convert_encoding($this->get('attach'), 'SJIS', mb_internal_encoding()) : mb_convert_encoding($this->get('attach'), 'EUC', mb_internal_encoding()) ;
				if(!is_file($attach)){
					$this->showError(sprintf("File Not Found[%s]", $attach));
					return false;
				}
				if(!is_readable($attach)){
					$this->showError(sprintf("File Not Readable[%s]", $attach));
					return false;
				}
			}
		}
		return true;
	}

	/**
	 *メールを送信する
	 * 
	 * @access public
	 * @return array
	 */
	public function send(){
		if(!$this->validateSendConfig()) return false;
		
		if($this->is_file_ex('Mail.php')){
			$mail = $this->getPear('Mail');
		}else{
			$this->showError('PEAR::Mailがインストールされていません');
			return false;
		}
		if($this->is_file_ex("Mail/mime.php")){
			$mime = $this->getPear('Mail_mime', "\n");
		}else{
			$this->showError('PEAR::mimeがインストールされていません');
			return false;
		}
		
		if(!$this->get('from')){
			$from = 'nobody@localhost';
		}else{
			$from = $this->get('from');
		}
		
		$subject = $this->get('subject') ? $this->get('subject') : '件名なし' ;
		
		if($this->get('encode')){
			$this->target_encode = $this->get('encode');
		}
		
		if($this->get('smtp')){
			$mail = Mail::factory("smtp", $this->get('smtp'));
		}else{
			$mail = Mail::factory("mail");
		}

		$this->set('body', mb_convert_encoding($this->get('body'), $this->target_encode, mb_internal_encoding()));

		$mime->setTxtBody($this->get('body'));
		
		if(is_array($this->get('attach'))){
			foreach($this->get('attach') as $val){
				$val = strpos(PHP_OS, 'WIN') !== false ? mb_convert_encoding($val, 'SJIS', mb_internal_encoding()) : mb_convert_encoding($val, 'EUC', mb_internal_encoding()) ;
				$mime->addAttachment($val);
			}
		}else{
			$val - $this->get('attach');
			$val = strpos(PHP_OS, 'WIN') !== false ? mb_convert_encoding($val, 'SJIS', mb_internal_encoding()) : mb_convert_encoding($val, 'EUC', mb_internal_encoding()) ;
			$mime->addAttachment($val);
		}

		$body = array(
		  "head_charset" => $this->target_encode,
		  "text_charset" => $this->target_encode
		);

		$this->set('body', mb_convert_encoding($mime->get($body), $this->target_encode, $this->source_encode));
		
		if($this->get('bcc') && count($this->get('bcc')) > 1){
			$bcc = implode(',', $this->get('bcc'));
			}elseif($this->get('bcc')){
			$bcc = $this->get('bcc');
			$bcc = $bcc[0];
		}

		if($this->get('cc') && count($this->get('cc')) > 1){
			$cc = implode(',', $this->get('cc'));
		}elseif($this->get('cc')){
			$cc = $this->get('cc');
			$cc = $cc[0];
		}

		$header = array(
			"To" => $this->get('mailto'),
			"From" => $from,
			"Bcc" => $bcc,
			"Cc" => $cc,
		  	"Subject" => mb_encode_mimeheader(mb_convert_encoding($subject, $this->target_encode), $this->target_encode, 'B')
		);

		$this->set('headers', $mime->headers($header));
		
		if($this->get('fetch') === true){
			$confirm = "差出人 : {$from}<br>送信先 : {$this->get('mailto')}<br>BCC : {$bcc}<br> CC : {$cc}<br><br>件名 : {$this->get('subject')}<br />{$this->get('body')}";
			return $confirm;
		}

		$ret = $mail->send($this->get('mailto'), $header, $this->get('body'));

		if(PEAR::isError($ret)){
			return $ret->getMessage();
		}
	}
	
	/**
	 * phpinfoのMailerクラス版です
	 *
	 */
	public function mailerinfo(){
		
		if($this->is_file_ex('Net/POP3.php') && $this->is_file_ex('Mail/mimeDecode.php')){
			$contents['メール受信'] = 'Enabled';
			$contents['Net/POP3'] = 'メールの受信が可能です';
			$contents['Mail/mimeDecode'] = '日本語メールのパースが可能です';
		}else{
			$contents['メール受信'] = 'Disabled';
		}
		
		if($this->is_file_ex('Mail.php') && $this->is_file_ex("Mail/mime.php")){
			$contents['メール送信'] = 'Enabled';
			$contents['Mail'] = 'メールの送信が可能です';
			$contents['Mail/mime'] = 'メールの送信の際に多機能化が可能です';
		}else{
			$contents['メール送信'] = 'Disabled';
		}
		
		if($this->is_file_ex('Smarty/Smarty.class.php')){
			$contents['定型文の作成'] = 'Enabled';
			$contents['Smarty'] = 'そもそもこの画面が見られるので大丈夫です';
		}
		
		$this->makeInfo("Mail_Mailer", "Mail_Mailer Function Enables List", $contents);
	}
	
	/**
	 * phpinfo()ライクなページを生成する
	 * 
	 * @param $name string ページ名
	 * @param $title string 情報タイトル(テーブルのキャプション)
	 * @param $contents array 表示する情報
	 * @access public
	 */
	function makeInfo($name, $title, $contents){
		$smarty = $this->initSmarty();
		$smarty->assign('name', $name);
		$smarty->assign('title', $title);
		$smarty->assign('contents', $contents);
		$smarty->display('info.tpl');
		//ここではinfoを表示するだけなので余計な処理をさせないように処理の強制終了
		exit;
	}
	
	//設定と受信で使うキー配列
	private $keys = array(
		'id',
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
		'deleteMsg',
		'encode',
		'template',
		'vars',
		'headers',
		'subject',
		'body',
		'filename',
		'file',
		'smtp',
		'fetch',
		'smarty',
	);
	
	/**
	 * キーを設定する
	 * @param $key キー名
	 * @param $val 値
	 * @access public
	 * @return プロパティに挿入
	 */
	public function set($key, $val=null){
		$r = $this->keyCheck($key);
		if($r === false){
			$this->showError('無効なキーです');
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
	
	/**
	 * CCを追加する
	 * @param $val 値
	 * @access public
	 * @return プロパティに挿入
	 */
	public function addCc($val){
		$this->keys['cc'][] = $val;
	}
	
	/**
	 * 添付ファイルを追加する
	 * @param $val 値
	 * @access public
	 * @return プロパティに挿入
	 */
	public function addAttach($val){
		$this->keys['attach'][] = $val;
	}
	
	/**
	 * 削除するメッセージを追加する
	 * @param $num メッセージ番号
	 * @access public
	 * @return プロパティに挿入
	 */
	public function addDelete($num){
		$this->keys['deleteMsg'][] = $num;
	}
	
	/**
	 * BCCを追加する
	 * @param $val 値
	 * @access public
	 * @return プロパティに挿入 
	 */
	public function addBcc($val){
		$this->keys['bcc'][] = $val;
	}

        /**
         * CCを削除する
         * @access public
         */
        public function clearCc(){
                $this->keys['cc'] = array();
        }

        /**
         * BCCを削除する
         * @access public
         */
        public function clearBcc(){
                $this->keys['bcc'] = array();
        }
        
        /**
         * 登録した添付ファイルを削除する
         * @access public
         */
        public function clearAttach(){
                $this->keys['attach'] = array();
        }
	
	/**
	 * 指定されたキーの値を取得する
	 * @param $key 取得するキー
	 * @access public
	 * @return string
	 */
	public function get($key){
		$r = $this->keyCheck($key);
		if($r === false){
			$this->showError("「{$key}」は無効なキーです");
			return false;
		}
		return $this->keys[$key];
	}
	
	/**
	 * 正しいキーかチェックする
	 * @param $key 取得するキー
	 * @access  private
	 * @return TRUE:成功 FALSE:失敗
	 */
	private function keyCheck($key){
		if(!in_array($key, $this->keys)){
			return false;
		}
		return true;
	}
	
	/**
	 * 設定情報を配列で返す
	 * @access public
	 * @return Array
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