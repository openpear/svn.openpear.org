<?php 
require_once("Net/UserAgent/Mobile.php");
require_once("Japanese_Mobile_Emoji.php");
require_once("Japanese_Mobile_Emoji_Convert.php");

ob_start();

echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<!DOCTYPE html PUBLIC "-//i-mode group (ja)//DTD XHTML i-XHTML(Locale/Ver.=ja/2.0) 1.0//EN" "i-xhtml_4ja_10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>絵文字実装テスト用ページ</title>

<style type="text/css">
<![CDATA[
a:focus{color:#FFFFFF}
a:visited{color:#FF3366}
a:link{color:#FF3366}
}
]]>
</style>

</head>
<body style="background-color:#FFFFFF;color:#000000">
<span style="font-size:small;">

<?php
echo "hello world";
if(!empty($_SERVER["HTTPS"]) == "on"){
	$this_url = "https://";
}else{
	$this_url = "http://";
}
$this_url .= $_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"]; 
?>

[emoji:1]
[emoji::1]
[emoji:::1]

<br />

<img src="http://chart.apis.google.com/chart?
chs=150x150&cht=qr&chl=<?php echo $this_url; ?>" />

<br />

<?php 
echo $this_url;
?>
</span>
</body>
</html><?php 
$bf = ob_get_clean();
$emoji = Japanese_Mobile_Emoji::singleton();
$emoji->setEmojiImg("/openper_JapaneseMobileEmoji/img/emoji/");
$bf = $emoji->doConvert2output($bf);
echo $bf;


function pr($val){
	echo "<pre>";
	print_r($val);
	echo "</pre>";
}
?>