<?php
/**
 * 「ログインできません」になるけど、WSSE認証のコードイメージは
 * こんな感じ。Services_Hatenaでもやってる。
 *
 * @see http://www.kiske.info/blog/archives/2007/04/phpatomapi.html
 * @see http://catbot.net/blog/2006/02/services_hatena_php.html
 */
error_reporting(E_ALL);
require_once 'HTTP/Request.php';

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


function execute($user, $pass, $url, $is_debug = false) {
    $nonce = pack('H*', sha1(md5(time().rand().posix_getpid())));
    $created = date('Y-m-d\TH:i:s') . 'Z';
    $digest = base64_encode(pack('H*', sha1($nonce . $created . $pass)));
    $wsse_header = sprintf('UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"', $user, $digest, base64_encode($nonce), $created);
    if ($is_debug) {
        var_dump($wsse_header);
    }

    $request = new HTTP_Request($url);
    $request->addHeader('X-WSSE', $wsse_header);

    if (PEAR::isError($request->sendRequest())) {
        die('request failed');
    }
    $response = $request->getResponseBody();
    if ($is_debug) {
        var_dump($response);
        echo '<hr>';
    }

    $xml = new SimpleXMLElement($response);
    return $xml;
}

$xml = execute($user, $pass, 'http://mixi.jp/atom/tracks/r=2/member_id=' . $id);
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
        $entry->link->href,
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
$xml = execute($user, $pass, 'http://mixi.jp/atom/friends/r=1/member_id=' . $id);


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
$xml = execute($user, $pass, 'http://mixi.jp/atom/updates/r=1/member_id=' . $id);


echo '<ul>';
foreach ($xml->entry as $entry) {
    switch ($entry->category['term']) {
    case 'diary':
        printf('<li>%s</li>', $entry->content);
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
            $entry->author->url,
            $entry->author->name,
            $entry->author->name,
            $entry->category['label']
        );
        break;
    default:
    }
}
