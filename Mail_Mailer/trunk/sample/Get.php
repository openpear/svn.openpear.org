<?php
mb_internal_encoding('UTF-8');
require_once('../Mailer.php');

//Mailerオブジェクトの生成
$mail = new Mail_Mailer();

$mail->set('user', '');
$mail->set('password', '');
$mail->set('host', '');
$mail->set('port', 110);
$mail->set('login', 'USER');
//$mail->set('encode', 'UTF-8');
//$config->set('search', 'example'); searchキーをセットするとメールサーバから該当メールだけを受信します(正規表現対応)
$mail->set('delete', false); //受信後にメールを削除するか デフォではfalseに trueで削除

//受信 //中身の表示は超適当なので気をつけて^^;
$mails = $mail->getMail();
//echo $mails[0]->get('id');
//$mail->addDelete(1);
//$mail->deleteMsg();

//foreachのために一応・・・
$mails = !is_array($mails) ? array() : $mails ;
foreach($mails as $val){
	$headers = $val->get('headers');
	echo "{$val->get('subject')}(<a href=\"mailto:{$headers['from']}\">{$headers['name']}</a>)<br />";
	echo nl2br($val->get('body'));
	echo '<hr>';
}

//$mails = $mail->getMail(true);
//print_r($mails);
?>