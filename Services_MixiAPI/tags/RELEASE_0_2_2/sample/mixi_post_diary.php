<?php
ini_set("include_path", dirname(__FILE__)."/src/" . PATH_SEPARATOR . ini_get("include_path"));
require_once 'Services/MixiAPI/Diary.php';
require_once 'Services/MixiAPI/Image.php';
require_once 'Services/MixiAPI/Factory.php';

error_reporting(E_ALL);

/**
 * $user,$pass,$idを適宜書き換えてください
 */
$user = '[mixiのログインID]';
$pass = '[mixiのパスワード]';
$id = '[mixiのユーザーID]';

/**
 * 日記オブジェクトの作成
 */
$title = 'Services_MixiAPIを使った投稿テスト';
$subject = <<< EOD
mixiも裏で色々と用意しているみたいですね。

「あしあと」がatom＋WSSE認証で配信されているようなので、試]]>してみました。WSSE認証については以下を参照ということで。

    * Web Services Security UsernameToken Profile 1.0（pdf）
    * はてなブックマークAtomAPIだのWSSEだの
    * はてなフォトライフatomapiとは - はてなダイアリー

以下サンプルですが、X-WSSEリクエストヘッダの内容を作っている部分は、PEAR::Services_HatenaとServices_Hatena - PHPの挑戦 (ハズレ日記)を参考にさせてもらいました。

つーか、ほとんどそのままですね ;-)
EOD;
$diary = new Services_MixiAPI_Diary($title, $subject);
$diary->setImage(new Services_MixiAPI_Image('./2905457_723594618.jpg'));

/**
 * 日記投稿APIオブジェクトの作成
 */
$service = Services_MixiAPI_Factory::getInstance(
               Services_MixiAPI_Factory::API_MODE_POSTDIARY,
               $user, $pass, $id);

/**
 * 日記オブジェクトをセット
 */
$service->setDiary($diary);

/**
 * リクエスト送信
 */
$service->execute();

/**
 * 結果のXML
 */
var_dump($service->get());
