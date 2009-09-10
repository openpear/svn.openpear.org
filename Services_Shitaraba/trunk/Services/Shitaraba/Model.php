<?php
	/*
		したらばの書き込み削除、倉庫送りを行う
		
		なんか実際に送受信とかする
	*/
	
class Services_Shitaraba_Model{
	
	//--------------------------------------------------------------------------------------
	//定数とか
		//このスクリプトの文字コード
			const SER_SHITARABA_ENCODE='UTF-8';
		
		//したらばの文字コード
			const SHITARABA_BBS_ENCODE='EUC-JP';
		
		//表側
			//したらばの場所
				const SHITARABA_BBS_BASE='http://jbbs.livedoor.jp/';
			//スレッド一覧
				const SHITARABA_BBS_THREAD='subject.txt';
			//各スレッド取得URL
				const SHITARABA_BBS_RES='bbs/rawmode.cgi/';
		
		//管理画面側
			//管理画面の場所
				const SHITARABA_MAN_BASE='http://cms.jbbs.livedoor.jp/';
			//ログイン
				const SHITARABA_MAN_LOGIN='login/';
			//スレッド倉庫送りURL-確認送信先
				const SHITARABA_MAN_THREAD_CONF='config/data/thread_delete';
			//スレッド倉庫送りURL-完了送信先
				const SHITARABA_MAN_THREAD_COMP='config/data/thread_delete_confirm';
			//個別削除URL
				const SHITARABA_MAN_DEL_CONF='config/data/thread';
			//個別削除URL
				const SHITARABA_MAN_DEL_COMP='config/data/thread_confirm';
	
	//--------------------------------------------------------------------------------------
	//インスタンス変数とか
		
		//したらばBBS情報
			//ジャンル名
			private $gerne=false;
			//掲示板番号
			private $bbsnum=false;
			//パスワード
			private $pw=false;
			//最大書き込み件数
			private $max_number=1000;
		
		//内部用
			//したらばと通信する用のクッキー
			private $cookie=false;
	
	//コンストラクタ
		public function __construct(){}
	
	//--------------------------------------------------------------------------------------
	//各パラメータセット
	public function setGerne($gerne=false){
		$this->gerne=$gerne;
		if(!$gerne){return false;}else{return true;}
	}
	public function setBbsnum($bbsnum=false){
		$this->bbsnum=$bbsnum;
		if(!$bbsnum){return false;}else{return true;}
	}
	public function setPw($pw=false){
		$this->pw=$pw;
		if(!$pw){return false;}else{return true;}
	}
	public function setMaxNumber($max_number){
		if(!$max_number || !is_int($max_number)){return false;}
		$this->max_number=$max_number;
		return true;
	}
	public function checkParam(){
		if(!$this->gerne || !$this->bbsnum){return false;}
		return true;
	}
	public function checkParamPw(){
		if(!$this->gerne || !$this->bbsnum || !$this->pw){return false;}
		return true;
	}
	
	//--------------------------------------------------------------------------------------
	//全スレッドの全データを取得
	public function getAllThreadData($ngword=false){
		//パラメータチェック
			if(!$this->checkParam()){return false;}
		
		//スレッドリスト取得
			$thread_data=$this->getThreadList();
			if(!$thread_data){return false;}
		
		//各スレッドに対して内容取得
			foreach($thread_data as $key=>$val){
				$thread_data[$key]['data']=$this->getThread($val['thread_id'],$ngword);
				if($ngword && !$thread_data[$key]['data']){
					unset($thread_data[$key]);
				}
			}
		
		//返却
			$thread_data=array_merge($thread_data);
			return $thread_data;
	}
	
	//--------------------------------------------------------------------------------------
	//ひとつのスレッドの内容を取得
	public function getThread($thread_id,$ngword=false){
		//パラメータチェック
			if(!$this->checkParam()){return false;}
		
		//URL組み立て
			$url_thread=
				 self::SHITARABA_BBS_BASE.self::SHITARABA_BBS_RES
				.$this->gerne.'/'.$this->bbsnum.'/'.$thread_id.'/';
			
		//取得して返却
			$ret_array=$this->_getThreadFromURL($url_thread,$ngword);
			return $ret_array;
	}
	
	//--------------------------------------------------------------------------------------
	//スレッドのリストを取得
	public function getThreadList(){
		//パラメータチェック
			if(!$this->checkParam()){return false;}
			$ret_array=array();
		
		//URL
			$url_thread_list=
				 self::SHITARABA_BBS_BASE.$this->gerne.'/'
				.$this->bbsnum.'/'.self::SHITARABA_BBS_THREAD;
		
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
				$ret_array[]=$ret;
				
			}
		
		//返却
			return $ret_array;
	}
	
	//--------------------------------------------------------------------------------------
	//dat落ち対象のスレッドリストを取得
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
				if($val['thread_num'] >= ($this->max_number)){
					$ret_array[]=$val;
				}
			}
		
		//返却
			return $ret_array;
	}

	//--------------------------------------------------------------------------------------
	//該当のデータを削除
	public function deleteNGThreadData($shitaraba_ng){
		
		//スレッド毎にforeach
		foreach($shitaraba_ng as $shitaraba_ng_key=>$shitaraba_ng_val){
			
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
	//該当のスレッドを倉庫送り
	public function datThread($dat_array){
		//スレッドIDだけ抽出
			$thread_id_array=array();
			foreach($dat_array as $val){
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
	//--------------------------------------------------------------------------------------
	//以下サブルーチン
	//--------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------
	//URLからスレッド内容取得
	protected function _getThreadFromURL($url_thread,$ngword=false){
		/*
			$url_thread	スレッドのURL
			$ngword		NGワードが入っていた場合、該当のレス番のみを返す
		*/
			$ret_array=array();
		
		//取得
			$thread_dat=@file($url_thread);
			if(!$thread_dat){return false;}
			
			//各レスに対して
			foreach($thread_dat as $val){
				
				//文字コード変換
					$val=trim(mb_convert_encoding($val,self::SER_SHITARABA_ENCODE,self::SHITARABA_BBS_ENCODE));
				
				//NGワードがあるなら該当のレスのみ抜き出し
					if($ngword){
						if(!$this->_isNGResData($val,$ngword)){
							continue;
						}
					}
				
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
	
	//--------------------------------------------------------------------------------------
	//NGワードチェック	getThread用
	protected function _isNGResData($thread_dat,$ngword){
		//引数
			if(!$thread_dat || !$ngword){return false;}
		
		//1は削除できない
			if(strpos($thread_dat,'1<>')===0){
				return false;
			}
		//isなので合ってしまった場合true
			if(strpos($thread_dat,$ngword)!==false){
				return true;
			}
		
		return false;
	}
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------------------
	//POSTする
	private function _POST($url,$post_array=false){
	
		/*
			file_get_contents、及びCurl系の関数は302のリダイレクトを正しく処理しないので
			(POSTが302で返ってきたらその先にGETで送ってしまう)
			したらばのログインにはそのままでは使用できません
			そういう設定が何処かにあるのかもしれんがよくわからん
			
			てかブラウザからだとリダイレクトにGET送っててOKなのにPHPからだとエラーになるんだが何故
		*/
	
		// Zend_Http_Client
			$http = new Zend_Http_Client(); 
		
		//設定
			$http->setUri($url); 
			
			//配列で来てたらsetParameterPost、文字列で来てたら生データを入れる
			if(is_array($post_array)){
				$http->setParameterPost($post_array);
			}else{
				$http->setRawData($post_array);
			}
			
			//302に対し正しくリダイレクトを行う
			$http->setConfig(array('strictredirects' => true));
			//cookieが存在すれば追加する
			if($this->cookie){
				$http->setCookie($this->cookie);
			}
		
		//実行
			$httpResponse = $http->request('POST');
			if(!$httpResponse->isSuccessful()){return false;}
		
		//Cookieを取得してセット
			$a=$http->getLastResponse();
			$a=$a->getHeaders();
			if($a['Set-cookie']){
				$this->cookie=Zend_Http_Cookie::fromString($a['Set-cookie']);
			}
		
		//返却
			return true;
	}
	
	//--------------------------------------------------------------------------------------
	//管理画面にログイン
		protected function _shitaraba_Login(){
			//パラメータチェック
				if(!$this->checkParamPw()){return false;}
				
			//ログインURL
				$login_url=self::SHITARABA_MAN_BASE
					.$this->gerne.'/'.$this->bbsnum.'/'.self::SHITARABA_MAN_LOGIN;
				
			//POSTするデータ
				$login_post=array(
					 'login_password'=>$this->pw
					,'login'=>'login'
				);
			//POSTするお
				$ret=$this->_POST($login_url,$login_post);
				
			//返却
				return $ret;
		}
		
	
	//--------------------------------------------------------------------------------------
	//スレ毎に投稿削除
	private function _shitaraba_Delete($thread_id,$res_id_array){
		//準備	引数はスレッドID、レスID配列
			if(!$thread_id || !is_array($res_id_array)){return false;}
			$form_post_string='';
		
		//管理画面にログイン
			$ret=$this->_shitaraba_Login();
			if(!$ret){return false;}
		
		//削除確認画面
			//URL
				$form_url=self::SHITARABA_MAN_BASE.$this->gerne.'/'.$this->bbsnum.'/'
					.self::SHITARABA_MAN_DEL_CONF.'?key='.$thread_id;
			
			//POSTパラメータ
				//削除するレス番号
				foreach($res_id_array as $val){
					$form_post_string.='res_num='.$val.'&';
				}
				//透明削除
				//$form_post_string.='invisible=on';
				//スレッドID
				$form_post_string.='key='.$thread_id;
		
			//POST
				$ret=$this->_POST($form_url,$form_post_string);
				if(!$ret){return false;}
		

		//削除実行
			//URL
				$form_url=self::SHITARABA_MAN_BASE.$this->gerne.'/'.$this->bbsnum.'/'
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
	private function _shitaraba_DatThread($thread_id_array){
		
		//管理画面にログイン
			$ret=$this->_shitaraba_Login();
			if(!$ret){return false;}
		
		//削除準備
			$ret=$this->_shitaraba_DatThread_conf($thread_id_array);
			if(!$ret){return false;}
		
		//削除実行
			return $this->_shitaraba_DatThread_comp();
	}
	
	
	//--------------------------------------------------------------------------------------
	//スレッドdat落ち	//確認画面
	private function _shitaraba_DatThread_conf($thread_id_array){
	
		//準備	引数はスレッドID配列
			if(!$thread_id_array || !is_array($thread_id_array)){return false;}
		
		//POSTパラメータ用意
			//URL
				$form_url=self::SHITARABA_MAN_BASE.$this->gerne.'/'.$this->bbsnum.'/'
					.self::SHITARABA_MAN_THREAD_CONF;
			//内容
				//dat落ちするスレッドID
				foreach($thread_id_array as $val){
					$form_post_array['key_'.$val]='on';
				}
		
		//POST
			return $this->_POST($form_url,$form_post_array);
	}
	
	
	//--------------------------------------------------------------------------------------
	//スレッドdat落ち	//完了画面
	private function _shitaraba_DatThread_comp(){
		
		//POSTパラメータ用意
			//URL
				$form_url=self::SHITARABA_MAN_BASE.$this->gerne.'/'.$this->bbsnum.'/'
					.self::SHITARABA_MAN_THREAD_COMP;
			
			//内容
				//削除するスレはconfで送っているので不要、下記はダミー
				$form_post_array=array('a'=>'a');
		
		//POST
			return $this->_POST($form_url,$form_post_array);
	}
	
	
	
	
	

//↓クラスのおわり
}