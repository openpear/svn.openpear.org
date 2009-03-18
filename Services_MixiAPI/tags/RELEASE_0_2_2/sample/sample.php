<?php
ini_set("include_path", dirname(__FILE__)."/src/" . PATH_SEPARATOR . ini_get("include_path"));
require_once "Services/MixiAPI/Factory.php";

error_reporting(E_ALL);

/**
 * $user,$pass,$idを適宜書き換えてください
 */
$user = '[mixiのログインID]';
$pass = '[mixiのパスワード]';
$id = '[mixiのユーザーID]';

$service = Services_MixiAPI_Factory::getInstance(
               Services_MixiAPI_Factory::API_MODE_FOOTPRINT,
               $user, $pass, $id);
$service->execute();
$xml = new SimpleXMLElement($service->get());
echo '<ul>';
foreach ($xml->entry as $entry) {
    $updated_iso8601 = $entry->updated;

    $updated = date('Y/m/d H:i:s', mktime(substr($updated_iso8601, 11, 2),
                                          substr($updated_iso8601, 14, 2),
                                          substr($updated_iso8601, 17, 2),
                                          substr($updated_iso8601, 5, 2),
                                          substr($updated_iso8601, 8, 2),
                                          substr($updated_iso8601, 0, 4)
                                          ));
    printf(
        '<li><a href="%s" title="%sさん">%s</a>さん (%s)</li>',
        $entry->link['href'],
        $entry->author->name,
        $entry->author->name,
        $updated
    );
}
echo '</ul>';
echo '<hr>';

/**
 * マイミク一覧
 */
$service = Services_MixiAPI_Factory::getInstance(
               Services_MixiAPI_Factory::API_MODE_MYMIXI,
               $user, $pass, $id);
$service->setLastLogin(60);
$service->execute();
$xml = new SimpleXMLElement($service->get());
echo '<ul>';
foreach ($xml->entry as $entry) {
    printf(
        '<li><a href="%s" title="%sさん (%s)">%s</a>さん (%s)</li>',
        $entry->link[0]['href'],
        $entry->title,
        $entry->category['label'],
        $entry->title,
        $entry->category['label']
    );
}
echo '</ul>';
echo '<hr>';

/**
 * マイミク日記、コミュニティなど最新更新一覧
 */
$service = Services_MixiAPI_Factory::getInstance(
               Services_MixiAPI_Factory::API_MODE_WHATSNEW,
               $user, $pass, $id);
$service->execute();
$xml = new SimpleXMLElement($service->get());
echo '<ul>';
foreach ($xml->entry as $entry) {
    switch ($entry->category['term']) {
    case 'diary':
        $updated_iso8601 = $entry->updated;

        $updated = date('Y/m/d H:i:s', mktime(substr($updated_iso8601, 11, 2),
                                              substr($updated_iso8601, 14, 2),
                                              substr($updated_iso8601, 17, 2),
                                              substr($updated_iso8601, 5, 2),
                                              substr($updated_iso8601, 8, 2),
                                              substr($updated_iso8601, 0, 4)
                                              ));
        printf('<li>%s %s</li>', $entry->content, $updated);
        break;
    case 'comment':
    case 'album':
    case 'video':
        printf(
            '<li><a href="%s" title="%s">%s</a> (<a href="%s" title="%s">%s</a>さんの%s)</li>',
            $entry->link['href'],
            $entry->title,
            $entry->title,
            $entry->author->url,
            $entry->author->name,
            $entry->author->name,
            $entry->category['label']
        );
        break;
    case 'bbs':
        printf(
            '<li><a href="%s" title="%s">%s</a> (「<a href="%s" title="%s">%s</a>」の%s)</li>',
            $entry->link['href'],
            $entry->title,
            $entry->title,
            $entry->author->uri,
            $entry->author->name,
            $entry->author->name,
            $entry->category['label']
        );
        break;
    default:
    }
}
