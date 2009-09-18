<?php
	/*
		サンプル何処に置けばいいのかわかんない
	*/
	
	//インスタンス作成
		require_once('Acme/IdolMaster.php');
		$imas=new Acme_IdolMaster();
	
	//全メンバーをループ
		foreach($imas as $key=>$val){
			$member_name[]=$val->family_name_ja;
		}
	
	//りっちゃんを取得
		$ricchan=$imas->get('AkizukiRitsuko');
		$ricchan=$imas->AkizukiRitsuko;
		$ricchan=$imas->AkizukiRitsuko();
	
	//りっちゃんのパラメータを取得
		$name[]=$imas->AkizukiRitsuko->get('family_name_ja');
		$name[]=$imas->AkizukiRitsuko->family_name_ja;
		$name[]=$ricchan->family_name_ja();
		
	//りっちゃんのパラメータを変更
		$imas->AkizukiRitsuko->family_name_ja('名前1');
		$ricchan->family_name_ja='名前2';
		$ricchan->set('family_name_ja','名前3');
	
	//りっちゃんを指定
		$imas->select('AkizukiRitsuko');
		$name[]=$imas->get('family_name_ja');
		$name[]=$imas->family_name_ja;
		$name[]=$imas->family_name_ja();
		$imas->set('family_name_ja','名前4');
		$imas->family_name_ja='名前5';
		$imas->family_name_ja('名前6');
	
	//指定解除
		$imas->select();
	