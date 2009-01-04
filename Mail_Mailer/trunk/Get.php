<?php
require_once('Mailer.php');
//mb_internal_encoding('UTF-8');

//Mailerオブジェクトの生成
$mail = & new Mailer();

//Mailer用設定オブジェクトの生成
$config = & $mail->getMailerConfig();
$config->set('user', '');
$config->set('password', '');
$config->set('host', '');
$config->set('port', 110);
$config->set('login', 'USER');
$config->set('encode', 'UTF-8');
//$config->set('search', 'example'); searchキーをセットするとメールサーバから該当メールだけを受信します(正規表現対応)
$config->set('delete', false); //受信後にメールを削除するか デフォではfalseに trueで削除

//受信
$mails = $mail->getMail($config);
print_r($mails);
?>