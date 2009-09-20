<?php
mb_internal_encoding('UTF-8');
require_once('../Mailer.php');
//Mailerオブジェクトの生成
$mail = new Mail_Mailer();

//SMTPを指定する場合
/*
$smtp = array(
	'host' => "localhost",
	'port' => "25",
	'auth' => false,
	'username' => "",
	'password' => "",
);
$mail->set('smtp', $smtp);
*/

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
$mail->set('mailto', 'example@example.com');
$mail->set('subject', 'お試し');

//CCを送る場合
//$mail->set('cc', 'cc_example@example.com');
//続けてsetすると複数の人にCCを送れるようになります
//$mail->set('cc', 'cc_example2@example.com');
//BCCを送る場合
//$mail->set('bcc', 'bcc_example@example.com');
//こちらも続けてsetする事により、複数の人にBCCを送れるようになります
//$mail->set('bcc', 'bcc_example2@example.com');

//CCを送る場合 Part2
//$mail->addCc('cc1@cc1.example.com');
//$mail->addCc('cc2@cc1.example.com');
//BCCを送る場合 Part2
//$mail->addBcc('bcc1@bcc1.example.com');
//$mail->addBcc('bcc1@bcc1.example.com');

//エンコード変える場合
$mail->set('encode', mb_internal_encoding());

$mail->addAttach(dirname(__FILE__) . '/sunrise.jpg');
$mail->addAttach(dirname(__FILE__) . '/日本語.zip');

$mail->empty_body_warning = true; //空の本文だとエラーを返すようにする 元からtrue

//そのまま送信(Smartyテンプレート経由)
//$r = $mail->send();
//送信内容の確認
$mail->set('fetch', true);
echo nl2br($mail->send());
//$mail->send();
//使える機能の確認(phpinfoに該当)
//$mail->mailerinfo();

//通常送信
//$mail->set('body', 'テスト');
//$r = $mail->send();

//入れた値を配列として取得する
//print_r($mail->getArray());
?>