<?php
	/*
		したらばの書き込み削除、倉庫送りを行う
		
		なんか実際に送受信とかする
	*/
	
require_once 'Services/Shitaraba/Model/Abstract.php';

class Services_Shitaraba_Model_Shitaraba extends Services_Shitaraba_Model_Abstract{
	
	//--------------------------------------------------------------------------------------
	//定数
		//このスクリプトの文字コード
			const SER_SHITARABA_ENCODE='UTF-8';
		
		//したらばの文字コード
			const SHITARABA_BBS_ENCODE='EUC-JP';
		
		//フロント
			//したらばの場所
				const SHITARABA_BBS_BASE='http://jbbs.livedoor.jp/';
			//スレッド一覧
				const SHITARABA_BBS_THREAD='subject.txt';
			//各スレッド取得URL
				const SHITARABA_BBS_RES='bbs/rawmode.cgi/';
		
		//管理画面
			//管理画面の場所
				const SHITARABA_MAN_BASE='http://cms.jbbs.livedoor.jp/';
			//ログイン
				const SHITARABA_MAN_LOGIN='login/';
			//スレッド倉庫送りURL-確認送信先
				const SHITARABA_MAN_THREAD_CONF='config/data/thread_delete_confirm';
			//スレッド倉庫送りURL-完了送信先
				const SHITARABA_MAN_THREAD_COMP='config/data/thread_delete_confirm';
			//個別削除URL
				const SHITARABA_MAN_DEL_CONF='config/data/thread';
			//個別削除URL
				const SHITARABA_MAN_DEL_COMP='config/data/thread_confirm';
	
	//--------------------------------------------------------------------------------------
	//変数
		private $bbsGenre = '';
		private $bbsNum = '';
		private $bbsPassword = '';
		private $bbsMaxNum = 1000;
		private $cookie = NULL;
		private $isLogon = false;
		
	//--------------------------------------------------------------------------------------
	//コンストラクタ
	public function __construct($bbsGenre = false, $bbsNum = false, $bbsPassword = false){
		if($bbsGenre){    $this->setGenre($bbsGenre); }
		if($bbsNum){      $this->setBbsnum($bbsNum); }
		if($bbsPassword){ $this->setPw($bbsPassword); }
	}
	
	//--------------------------------------------------------------------------------------
	//ジャンル名(カテゴリ名)をセット
	public function setGenre($bbsGenre = false){
		$this->bbsGenre = $bbsGenre;
		return true;
	}
	public function setGerne($bbsGenre = false){
		return $this->setGenre($bbsGenre);
	}
	//掲示板番号をセット
	public function setBbsnum($bbsNum = false){
		$this->bbsNum = $bbsNum;
		return true;
	}
	//パスワードをセット
	public function setPw($bbsPassword = false){
		$this->bbsPassword = $bbsPassword;
		return true;
	}
	
	//--------------------------------------------------------------------------------------
	//全発言数を指定
	public function setMaxNumber($bbsMaxNum){
		if(!$bbsMaxNum || !is_int($bbsMaxNum) || $bbsMaxNum < 1 ){return false;}
		$this->bbsMaxNum = $bbsMaxNum;
		return true;
	}
	
	//--------------------------------------------------------------------------------------
	//DAT落ちさせるスレッドのリストを取得
	public function getDatThreadList(){
		
		//パラメータチェック
			if(!$this->checkParam()){return false;}
			$ret_array=array();
		
		//スレッドリストを取得
			$thread_list=$this->getThreadList();
			if($thread_list===false){return false;}
		
		//対象を選択
			foreach($thread_list as $val){
				//最大書き込み件数以上が対象
				if($val['thread_num'] >= ($this->bbsMaxNum)){
					$ret_array[]=$val;
				}
			}
		
		//返却
			return $ret_array;
		
		
	}
	
	//--------------------------------------------------------------------------------------
	//DAT落ちを実行する
	public function datThread($threadIdArray){
		
		//スレッドIDだけ抽出
			$thread_id_array=array();
			foreach($threadIdArray as $val){
				if(is_numeric($val)){
					$thread_id_array[]=(string)$val;
				}elseif(isset($val['thread_id'])){
					$thread_id_array[]=$val['thread_id'];
				}
			}
			if(!$thread_id_array){
				return false;
			}
		
		//削除して返却
			return $this->_shitaraba_DatThread($thread_id_array);
			
	}
	
	//--------------------------------------------------------------------------------------
	//スレッドのリストを取得する
	public function getThreadList(){
	
		//パラメータチェック
			if(!$this->checkParam()){return false;}
			$ret_array=array();
		
		//URL
			$url_thread_list=
				 self::SHITARABA_BBS_BASE.$this->bbsGenre.'/'
				.$this->bbsNum.'/'.self::SHITARABA_BBS_THREAD;
		
		//取得
			$thread_list=@file($url_thread_list);
			if(!$thread_list){return false;}
			//なんか知らんが同じデータが入ってくることがあるので減らす
			$thread_list=array_unique($thread_list);
		
		//文字コード変換、配列に展開
			foreach($thread_list as $val){
				$val=trim(mb_convert_encoding($val,self::SER_SHITARABA_ENCODE,self::SHITARABA_BBS_ENCODE));
				$tmp=explode('.cgi,',$val,2);
				$ret['thread_id']=$tmp[0];
				
				$tmp=preg_match('/^(.*)\(([0-9]+)\)\z/',$tmp[1],$matches);
				$ret['thread_title']=$matches[1];
				$ret['thread_num']=$matches[2];
				$ret_array[$ret['thread_id']]=$ret;
			}
		
		//返却
			return $ret_array;
	}
	
	//--------------------------------------------------------------------------------------
	//スレッドIDを指定して内容を取得する
	public function getThread($threadId, $ngWord = ''){
		
		//パラメータチェック
			if(!$this->checkParam()){return false;}
		
		//URL組み立て
			$url_thread=
				 self::SHITARABA_BBS_BASE.self::SHITARABA_BBS_RES
				.$this->bbsGenre.'/'.$this->bbsNum.'/'.$threadId.'/';
			
		//取得
			$ret = $this->_getThreadFromURL($url_thread);
		
		//NGワードチェック
		if($ngWord){
			//NGワードが入っていなければ削除、また1であれば削除
			//NGワードが入っている発言のみを残すため	(1は削除できない)
			foreach($ret as $key=>$val){
				if(strpos($val['res_value'], $ngWord) ===false || $val['res_id']==='1'){
					unset($ret[$key]);
				}
			}
		}
		
		//終了
			return $ret;
	}
	
	//--------------------------------------------------------------------------------------
	//NGワードが含まれる発言を取得
	public function getNGThreadData($ngWord){
		if(!is_string($ngWord)){return false;}
		$retArray = $this->getAllThreadData($ngWord);
		
		//対象がひとつもなければスレッドを削除
		foreach($retArray as $key=>$val){
			if(!$val['data']){
				unset($retArray[$key]);
			}
		}
		
		return $retArray;
	}
	
	//--------------------------------------------------------------------------------------
	/*
	* 任意の発言を削除する
	* @param  int    スレッドID
	* @param  array  レス番号の配列
	* @return bool
	**/
	public function deleteThreadData($threadId, array $resIdArray = array()){
		if(!$threadId || !$resIdArray){
			return false;
		}
		
		return $this->_shitaraba_Delete($threadId, $resIdArray);
	}
	
	//--------------------------------------------------------------------------------------
	//NGワードが含まれる発言を削除
	public function deleteNGThreadData($ngThreadData){
		
		//スレッド毎にforeach
		foreach($ngThreadData as $shitaraba_ng_key=>$shitaraba_ng_val){
			
			//スレ番
				$stitaraba_thread_id=$shitaraba_ng_val['thread_id'];
				if(!$stitaraba_thread_id){continue;}
			
			//レス番を抽出
				$stitaraba_res_id=array();
				foreach($shitaraba_ng_val['data'] as $key=>$val){
					$stitaraba_res_id[]=$key;
				}
				if(!$stitaraba_res_id){continue;}
			//スレ毎に削除実行	引数はスレ番、レス番array
				$this->_shitaraba_Delete($stitaraba_thread_id,$stitaraba_res_id);
		}
		return true;
	}
	
	//--------------------------------------------------------------------------------------
	//全スレッドの全データを取得
	public function getAllThreadData($ngWord = false){
		
		//パラメータチェック
			if(!$this->checkParam()){return false;}
		
		//スレッドリスト取得
			$thread_data=$this->getThreadList();
			if(!$thread_data){return false;}
			
		//各スレッドに対して内容取得
			foreach($thread_data as $key=>$val){
				$thread_data[$key]['data']=$this->getThread($val['thread_id'],$ngWord);
			}
		
		//返却
			return $thread_data;
	}
	
	//--------------------------------------------------------------------------------------
	/*
	* スレッドIDを指定して内容を取得する
	* 管理画面側の内容で、ホスト名も取得
	* @param  int    スレッドID
	* @param  string NGワード、入っていれば該当の発言「のみ」取り出す
	* @param  string NGホスト、入っていれば該当の発言「のみ」取り出す
	* @return array
	* 
	**/
	public function getThreadAdmin($threadId, $ngWord = '', $ngHost = ''){
		require_once('Zend/Dom/Query.php');
		
		//パラメータチェック
			if(!$this->checkParamPw()){return false;}
		
		//管理画面にログイン
			$ret=$this->_shitaraba_Login();
		
		//URL組み立て
			$form_url=self::SHITARABA_MAN_BASE.$this->bbsGenre.'/'.$this->bbsNum.'/'
				.self::SHITARABA_MAN_DEL_CONF.'?key='.$threadId;
			
		//取得
			$delConfHtml = $this->_GET($form_url, NULL, true);
			if(!$delConfHtml){return false;}
		
		//帰ってきたHTMLをパースできる形式に
			//EUCなのでUTF8に
			$delConfHtml = mb_convert_encoding($delConfHtml, self::SER_SHITARABA_ENCODE, self::SHITARABA_BBS_ENCODE);
			$delConfHtml = str_replace("EUC-JP", "UTF-8", $delConfHtml);
			//何故かトリップの<b>と</b>が逆	→	とりあえず放置
		
		//Tidyでパース
			$tidy = new tidy;
			$config = array('indent' => TRUE,
                'output-xhtml' => TRUE,
                'wrap' => 200
				,'escape-cdata'=>true
				);
			$tidy->parseString($delConfHtml, $config, 'utf8');
			$tidyHtml = $tidy->html();
			
			//レスの部分を取得
				//
				$tidyResTmp = $tidyHtml->child[1]->child[0]->child[0]->child[4]->child[1]->child[6];
				$tidyResTable = $tidyResTmp->child[0];
				//テーブルタグでなければchild[1]になる	レス数が多いと「透明削除」列が増えてしまうため
				if($tidyResTable->id !== TIDY_TAG_TABLE){
					$tidyResTable = $tidyResTmp->child[1];
				}
				unset($tidyHtml);
				unset($tidy);
		
		//各レスについてくるくる
			/*
				TidyNodeは
					・countできない	(1になる)
					・foreachできない	(プロパティが入ってくる)
				とちょっと扱いにくい
			*/
		//$this->bbsMaxNum=3;
		
		
		$returnArray = array();
		for($loop = 1; $loop<=$this->bbsMaxNum; $loop++){
			$tidyRes = $tidyResTable->child[$loop];
			if(!$tidyRes){break;}
			
			//必要な部分を取得
				//ホスト名
					$tidyResHostName = trim($tidyRes->child[3]->child[1]->child[0]->value);
					if(!$tidyResHostName){continue;}
					
					//NGホストがあれば
					if($ngHost){
						//NGでなければパス
						if(strpos($tidyResHostName, $ngHost)===false){
							continue;
						}
					}
				
				//本文
					$tidyResBody = trim($tidyRes->child[3]->child[5]->value);
					//周囲の<span>タグも入ってきてしまうので外す
					$tidyResBody = preg_replace('/\r\n|\r|\n/', '', $tidyResBody);
					$tidyResBody = preg_replace('|^<span class="message">(.*)</span>$|', '\1', $tidyResBody);
					//NGワードがあれば
					if($ngWord){
						//NGでなければパス
						if(strpos($tidyResBody, $ngWord)===false){
							continue;
						}
					}
				
				//レス番
					$tidyResID = trim($tidyRes->child[1]->child[0]->value);
				
				//名前とメル欄
					$tidyResMail = trim($tidyRes->child[2]->child[2]->child[0]->value);
					
					//トリップがある場合012、ないときは01になる
					if($tidyResMail){
						$tidyResName = trim($tidyRes->child[2]->child[0]->child[0]->value)
										.' '.trim($tidyRes->child[2]->child[1]->value);
					}else{
						$tidyResName = trim($tidyRes->child[2]->child[0]->child[0]->value);
						$tidyResMail = trim($tidyRes->child[2]->child[1]->child[0]->value);
					}
					
				//ID
					$tidyResUid = trim($tidyRes->child[3]->child[3]->child[0]->value);
					$tidyResUid = str_replace('&nbsp;&nbsp;ID:', '', $tidyResUid);
				
				//投稿日時
					$tidyResDate = trim($tidyRes->child[3]->child[0]->child[0]->value);
				
			//返り値
				$tmpArray = array(
					 'res_id'     => $tidyResID
					,'hostname'   => $tidyResHostName
					,'res_name'   => $tidyResName
					,'res_user_id'=> $tidyResUid
					,'res_value'  => $tidyResBody
					,'res_date'   => $tidyResDate
					,'res_mail'   => $tidyResMail
				);
				$returnArray[$tidyResID] = $tmpArray;
		}
		
		//終了
			return $returnArray;
	}
	
	
	//--------------------------------------------------------------------------------------
	//以下内部で使用
	//--------------------------------------------------------------------------------------
	
	
	//--------------------------------------------------------------------------------------
	//GETする
	protected function _GET($url, $post_array = false, $returnString = false){
		return $this->_POST($url, $post_array, true, $returnString);
	}
	//--------------------------------------------------------------------------------------
	//POSTする	
	protected function _POST($url, $post_array = false, $is_get = false, $returnString = false){
	
		/*
			file_get_contents、及びCurl系の関数は302のリダイレクトを正しく処理しないので
			(POSTが302で返ってきたらその先にGETで送ってしまう)
			したらばのログインにはそのままでは使用できません
			そういう設定が何処かにあるのかもしれんがよくわからん
			
			てかブラウザからだとリダイレクトにGET送っててOKなのにPHPからだとエラーになるんだが何故
			
			setCookieJarとか使うと楽そう
		*/
	
		// Zend_Http_Client
			$http = new Zend_Http_Client(); 
		
		//設定
			$http->setUri($url); 
			
			//配列で来てたらsetParameterPost、文字列で来てたら生データを入れる
			if($is_get===true){
				if(is_array($post_array)){
					$http->setParameterGet($post_array);
				}else{
					$http->setRawData($post_array);
				}
			}else{
				if(is_array($post_array)){
					$http->setParameterPost($post_array);
				}else{
					$http->setRawData($post_array);
				}
			}
			
			//302に対し正しくリダイレクトを行う
			$http->setConfig(array('strictredirects' => true));
			//cookieが存在すれば追加する
			if($this->cookie){
				$http->setCookie($this->cookie);
			}
		
		//実行
			if($is_get===true){
				$httpResponse = $http->request('GET');
			}else{
				$httpResponse = $http->request('POST');
			}
			if(!$httpResponse->isSuccessful()){return false;}
		//Cookieを取得してセット
			$a = $http->getLastResponse();
			$a = $a->getHeaders();
			if($a['Set-cookie']){
				$this->cookie = Zend_Http_Cookie::fromString($a['Set-cookie']);
			}
			
		//返却
			if($returnString === true){
				return $httpResponse->getBody();
			}else{
				return true;
			}
	}
	
	//--------------------------------------------------------------------------------------
	//設定確認
	public function checkParam(){
		if(!$this->bbsGenre || !$this->bbsNum){return false;}
		return true;
	}
	public function checkParamPw(){
		if(!$this->bbsGenre || !$this->bbsNum || !$this->bbsPassword){return false;}
		return true;
	}
	
	//--------------------------------------------------------------------------------------
	//管理画面にログイン
	protected function _shitaraba_Login(){
		//ログイン済
		if($this->isLogon === true){
			return true;
		}
		
		//パラメータチェック
			if(!$this->checkParamPw()){return false;}
			
		//ログインURL
			$login_url=self::SHITARABA_MAN_BASE
				.$this->bbsGenre.'/'.$this->bbsNum.'/'.self::SHITARABA_MAN_LOGIN;
			
		//POSTするデータ
			$login_post=array(
				 'login_password'=>$this->bbsPassword
				,'login'=>'login'
			);
			
		//POSTする
			$ret = $this->_POST($login_url,$login_post);
		
		//ログイン済
			if($ret){
				$this->isLogon = true;
			}else{
				$this->isLogon = false;
			}
		
		//返却
			return $ret;
	}
	
	
	//--------------------------------------------------------------------------------------
	//スレ毎に投稿削除
	protected function _shitaraba_Delete($thread_id, $res_id_array, $isInvisible = false){
		//準備	引数はスレッドID、レスID配列
			if(!$thread_id || !is_array($res_id_array)){return false;}
			$form_post_string='';
		
		//管理画面にログイン
			$ret=$this->_shitaraba_Login();
			if(!$ret){return false;}
		
		//削除確認画面
			//URL
				$form_url=self::SHITARABA_MAN_BASE.$this->bbsGenre.'/'.$this->bbsNum.'/'
					.self::SHITARABA_MAN_DEL_CONF.'?key='.$thread_id;
			
			//POSTパラメータ
				//削除するレス番号
				foreach($res_id_array as $val){
					$form_post_string.='res_num='.$val.'&';
				}
				//透明削除
				if($isInvisible ===true){
					$form_post_string.='invisible=on&';
				}
				//スレッドID
				$form_post_string.='key='.$thread_id;
		
			//POST
				$ret=$this->_POST($form_url,$form_post_string);
				if(!$ret){return false;}
		
		//削除実行
			//URL
				$form_url=self::SHITARABA_MAN_BASE.$this->bbsGenre.'/'.$this->bbsNum.'/'
					.self::SHITARABA_MAN_DEL_COMP;
			
			//POSTパラメータは不要なのでダミー
				$form_post_array=array('1'=>'1');
			
			//POST
				$ret=$this->_POST($form_url,$form_post_array);
		
		//終了
				return $ret;
	}
	
	
	//--------------------------------------------------------------------------------------
	//スレ毎に投稿dat落ち	実行
	protected function _shitaraba_DatThread($thread_id_array){
		
		//管理画面にログイン
			$ret=$this->_shitaraba_Login();
			if(!$ret){return false;}
		
		//全スレッドに対し
		foreach($thread_id_array as $key=>$thread_id){
			//削除準備
				$ret=$this->_shitaraba_DatThread_conf($thread_id);
				//if(!$ret){return false;}
			
			//削除実行
				$ret=$this->_shitaraba_DatThread_comp($thread_id);
				//if(!$ret){return false;}
		}
		
		//終了
			return true;
	}
	
	//--------------------------------------------------------------------------------------
	//スレッドdat落ち	//確認画面
	protected function _shitaraba_DatThread_conf($thread_id){
	
		//準備	引数はスレッドID単品
			if(!$thread_id ){return false;}
		
		//POSTパラメータ用意
			//URL
				$form_url = self::SHITARABA_MAN_BASE.$this->bbsGenre.'/'.$this->bbsNum.'/'
					.self::SHITARABA_MAN_THREAD_CONF;
			//内容
				//dat落ちするスレッドID
					$form_post_array['key'] = $thread_id;
				//過去ログ送り
				$form_post_array['subcommand']='save';
		//POST
			return $this->_GET($form_url,$form_post_array);
	}
	
	
	//--------------------------------------------------------------------------------------
	//スレッドdat落ち	//完了画面
	protected function _shitaraba_DatThread_comp($thread_id){
		
		//POSTパラメータ用意
			//URL
				$form_url=self::SHITARABA_MAN_BASE.$this->bbsGenre.'/'.$this->bbsNum.'/'
					.self::SHITARABA_MAN_THREAD_COMP;
			
			//内容
				//削除するスレはconfで送っているので不要
				$form_post_array=array('a'=>'a');
		
		//POST
			return $this->_POST($form_url,$form_post_array);
	}
	
	
	//--------------------------------------------------------------------------------------
	//URLからスレッド内容取得
	protected function _getThreadFromURL($url_thread){
			$ret_array=array();
		
		//取得
			$thread_dat=@file($url_thread);
			if(!$thread_dat){return false;}
			
			//各レスに対して
			foreach($thread_dat as $val){
				
				//文字コード変換
					$val=trim(mb_convert_encoding($val,self::SER_SHITARABA_ENCODE,self::SHITARABA_BBS_ENCODE));
				
				//配列に展開
					$tmp=explode('<>',$val,7);
					$ret['res_id']=$tmp[0];
					$ret['res_name']=$tmp[1];
					$ret['res_mail']=$tmp[2];
					$ret['res_date']=$tmp[3];
					$ret['res_value']=$tmp[4];
					$ret['res_user_id']=$tmp[6];
				
				//返り値をセット
				//希に不正なエンコーディングで↑が正しく取得できない場合がある。どうしたものやら
					$ret_array[$tmp[0]]=$ret;
			}
		return $ret_array;
	}
	
}