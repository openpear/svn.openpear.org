<?php
//SMTPを指定する場合
/*
$smtp = array(
	'host' => "localhost",
	'port' => "25",
	'auth' => false,
	'username' => "",
	'password' => "",
);
*/
$smtp = null;

$file = null;

//Smartyにセットする情報を設定
//BODYキー : $config->set('body', 'テスト')に値を空にすると自動的にSmarty経由で処理させるようになります
$config->set('template', 'mail_template.tpl');
//Smarty変数の設定
$vars = array(
		'name' => 'テスト',
		'date' => date('Y年m月d日'),
		'array' => array(
			'おはよう',
			'こんにちは',
			'こんばんは',
		)
	);
$config->set('vars', $vars);
$config->set('mailto', '');
$config->set('subject', '件名');

//CCを送る場合
$config->set('cc', 'cc_example@example.com');
//続けてsetすると複数の人にCCを送れるようになります
$config->set('cc', 'cc_example2@example.com');
//BCCを送る場合
$config->set('bcc', 'bcc_example@example.com');
//こちらも続けてsetする事により、複数の人にBCCを送れるようになります
$config->set('bcc', 'bcc_example2@example.com');

$mail->empty_body_warning = true; //空の本文だとエラーを返すようにする 元からtrue

//$r = $mail->send($config, $smtp);

$config->set('body', 'テスト');

//$r = $mail->send($config, $smtp);

//入れた値を配列として取得する
//print_r($config->getArray());
?>