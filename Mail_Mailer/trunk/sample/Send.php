<?php
mb_internal_encoding('UTF-8');
require_once('../Mailer.php');
//Mailerオブジェクトの生成
$mail = & new Mail_Mailer();

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
$mail->set('template', 'mail_template.tpl');
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
$mail->set('vars', $vars);
$mail->set('mailto', '');
$mail->set('subject', '件名');

//CCを送る場合
//$mail->set('cc', 'cc_example@example.com');
//続けてsetすると複数の人にCCを送れるようになります
//$mail->set('cc', 'cc_example2@example.com');
//BCCを送る場合
//$mail->set('bcc', 'bcc_example@example.com');
//こちらも続けてsetする事により、複数の人にBCCを送れるようになります
//$mail->set('bcc', 'bcc_example2@example.com');

//CCを送る場合 Part2
$mail->addCc('');

//BCCを送る場合 Part2
$mail->addBcc('');

$mail->empty_body_warning = true; //空の本文だとエラーを返すようにする 元からtrue

$r = $mail->send($smtp);

$mail->set('body', 'テスト');

//$r = $mail->send($smtp);

//入れた値を配列として取得する
//print_r($mail->getArray());
?>