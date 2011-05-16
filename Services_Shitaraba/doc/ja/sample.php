<?php
	
	//OpenPear/Services/Shitaraba
		require_once('Services/Shitaraba.php');
	//引数はジャンル、掲示板番号、管理画面パスワード
		$shitaraba=Services_Shitaraba::factory('genre','123456789','password');
	
	//各パラメータを個別にセット
		$shitaraba->setGerne('genre');
		$shitaraba->setBbsnum('123456789');
		$shitaraba->setPw('password');
	
	//全スレッドリストを取得
		$ret=$shitaraba->getThreadList();
		
	//特定のスレッドを取得
		$ret=$shitaraba->getThread('123456789');
		
	//全スレッドの全データを取得
		$ret=$shitaraba->getAllThreadData();
	
	//NGワードが含まれる発言を取得
		$ret=$shitaraba->getNGThreadData('あああああ');
	
	//NGワードが含まれる発言を削除
		$ret=$shitaraba->deleteNGThreadData(
			$shitaraba->getNGThreadData('あああああ')
		);
	
	//全発言数を指定(1000以外に変更している場合に使用)
		$ret=$shitaraba->setMaxNumber(2000);
	
	//1000(setMaxNumber)行ったスレを取得
		$ret=$shitaraba->getDatThreadList();
	
	//1000(setMaxNumber)行ったスレをdat落ちさせる
		$ret=$shitaraba->datThread(
			$shitaraba->getDatThreadList()
		);
	
	//特定のスレをdat落ちさせる
		$ret=$shitaraba->datThread(
			array(123456,234567)
		);
	
	/*管理画面からスレッドの中身を取得
	* @param  int    スレッドID
	* @param  string NGワード、入っていれば該当の発言「のみ」取り出す
	* @param  string NGホスト、入っていれば該当の発言「のみ」取り出す
	**/
		$ret=$shitaraba->getThreadAdmin($threadId, $ngWord, $ngHost);