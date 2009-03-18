<?php
ini_set("include_path", dirname(__FILE__)."/src/" . PATH_SEPARATOR . ini_get("include_path"));
require_once 'Services/MixiAPI/Image.php';
require_once 'Services/MixiAPI/Factory.php';

error_reporting(E_ALL);

//$user = '[mixiのログインID]';
//$pass = '[mixiのパスワード]';
//$id = '[mixiのユーザーID]';
$user = getenv('SERVICES_MIXI_USER');
$pass = getenv('SERVICES_MIXI_PWD');
$id = getenv('SERVICES_MIXI_ID');
if (!$user) {
    die('user is not set');
}
if (!$pass) {
    die('password is not set');
}
if (!$id) {
    die('id is not set');
}



/**
 * フォトアルバムAPIオブジェクトの作成
 */
$service = Services_MixiAPI_Factory::getInstance(
               Services_MixiAPI_Factory::API_MODE_ALBUMLIST,
               $user, $pass, $id);

/**
 * リクエスト送信
 */
$service->execute();

/**
 * 結果のXMLからアルバム名を取得
 */
$xml = new SimpleXMLElement($service->get());
$xml->registerXPathNamespace('default', 'http://www.w3.org/2007/app');
$xml->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
foreach ($xml->xpath('//default:collection[contains(@href, "/album_id=")]/atom:title') as $element) {
    echo $element . "\n";
}



/**
 * フォトアルバムAPIオブジェクトの作成
 */
$service = Services_MixiAPI_Factory::getInstance(
               Services_MixiAPI_Factory::API_MODE_PHOTOALBUM,
               $user, $pass, $id);

/**
 * アルバムIDをセット
 */
$service->setAlbumId('18709833');

/**
 * リクエスト送信
 */
$service->execute();

/**
 * 結果のXMLから写真タイトルとURLを取得
 */
$xml = new SimpleXMLElement($service->get());
$xml->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
foreach ($xml->xpath('//atom:entry') as $element) {
    printf("%s(%s)\n", $element->title, $element->link['href']);
}



/**
 * メソッドをセット
 * デフォルトはServices_MixiAPI_PhotoAlbum::METHOD_LIST
 */
$service->setMethod(Services_MixiAPI_PhotoAlbum::METHOD_POST);

/**
 * 写真オブジェクトをセット
 */
$service->setImage(new Services_MixiAPI_Image('./2905457_723594618.jpg'));

/**
 * リクエスト送信
 */
$service->execute();

/**
 * 結果のXML
 */
var_dump($service->get());
