<?php

/**
 * Services_Shitaraba
 *
 * @author  NurseAngel<m@m.ll.to>
 * @package openpear
 * @version $Id: Services_Shitaraba.php 3 2009-09-08 12:23:56Z NurseAngel $
 */
 
	//--------------------------------------------------------------------------------------
	/*
		
	*/
	//--------------------------------------------------------------------------------------
	
	

//--------------------------------------------------------------------------------------
//インクルード
	
	//Zend_Http_Client
		require_once('Zend/Http/Client.php');
		require_once('Zend/Http/Cookie.php');
	
	//Services_Shitaraba_Model
		require_once('Services/Shitaraba/Model.php');

//--------------------------------------------------------------------------------------
//クラス
class Services_Shitaraba{
	/*
		したらばの書き込み削除、倉庫送りを行うときにここのメソッドを呼び出す
		
		中身は単にServices_Shitaraba_Modelに渡してるだけ
		なんでこんな作りにしたんだろう？
	*/
	
	//--------------------------------------------------------------------------------------
	//コンストラクタ
	public function __construct($gerne=false,$bbsnum=false,$pw=false){
		//Services_Shitaraba_Model
			$this->model=new Services_Shitaraba_Model();
		//引数があればセット
			if($gerne){$this->setGerne($gerne);}
			if($bbsnum){$this->setBbsnum($bbsnum);}
			if($pw){$this->setPw($pw);}
	}
	
	//--------------------------------------------------------------------------------------
	//各パラメータセット
	public function setMaxNumber($max_number){
		/*
			書き込み最大件数
			1000以外に設定してある場合に指定してください
		*/
		return $this->model->setMaxNumber($max_number);
	}
	public function setGerne($gerne=false){
		/*
			ジャンル	computerとかgameとかそういうあれ
		*/
		return $this->model->setGerne($gerne);
	}
	public function setBbsnum($bbsnum=false){
		/*
			ジャンルの次に来る掲示板番号
		*/
		return $this->model->setBbsnum($bbsnum);
	}
	public function setPw($pw=false){
		/*
			管理画面のログインパスワード
		*/
		return $this->model->setPw($pw);
	}
	
	//--------------------------------------------------------------------------------------
	//全スレッドの全データを取得
	public function getAllThreadData(){
			return $this->model->getAllThreadData();
	}
	
	//--------------------------------------------------------------------------------------
	//スレッドのリストを取得
	public function getThreadList(){
			return $this->model->getThreadList();
	}
	
	//--------------------------------------------------------------------------------------
	//ひとつのスレッドの内容取得
	public function getThread($thread_id,$ngword=false){
		/*
			$thread_id		取得するスレッドのID
			$ngword			入っている場合、NGワードが含まれるレスのみを取得
		*/
			return $this->model->getThread($thread_id,$ngword);
	}
	
	//--------------------------------------------------------------------------------------
	//NGワードの含まれるスレッド、レスを全部取得
	public function getNGThreadData($ngword){
		/*
			$ngword			NGワードが含まれるスレッド、レスを全部取得
		*/
		if(!is_string($ngword)){return false;}
		return $this->model->getAllThreadData($ngword);
	}
	
	//--------------------------------------------------------------------------------------
	//該当のデータを削除
	public function deleteNGThreadData($ng_array){
		/*
			$ng_arrayは以下のような配列
				array(
					array(
						 'thread_id'='123456789'
						,'data'=array(
							 10
							,20
							,30
						)
					)
					,…
				)
			
			あるいはgetNGThreadDataの返り値をそのまま入れます
				$services_shitaraba->deleteNGThreadData(
					$services_shitaraba->getNGThreadData('hoge')
				)
			
			
			なんかオブジェクトでできるようにしたい
		*/
			return $this->model->deleteNGThreadData($ng_array);
	}
	
	//--------------------------------------------------------------------------------------
	//dat落ち対象のスレッドリストを取得
	public function getDatThreadList(){
			return $this->model->getDatThreadList();
	}
	
	//--------------------------------------------------------------------------------------
	//該当のスレッドを倉庫送り
	public function datThread($thread_id_array){
		/*
			$thread_id_arrayは倉庫送りするスレッドの配列
				array(123456789,234567890,345678901)
			
			あるいはgetDatThreadListの返り値をそのまま入れます
				$services_shitaraba->datThread(
					$services_shitaraba->getDatThreadList()
				)
			
			
			これもオブジェクトにしたいにゃー
		*/
			return $this->model->datThread($thread_id_array);
	}
	
		
//↓クラスのおわり
}