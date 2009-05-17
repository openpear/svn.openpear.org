<?php
/**
 * PHPマニュアルをパースしてCSVファイルを作成するサンプル
 *
 * http://jp.php.net/download-docs.php
 * 
 * 一つのhtmlファイルとして配布されているPHPマニュアルから
 * 関数の説明部分をパースし、CSVファイルを作成する。
 * ファイルはあらかじめダウンロードして解凍しておく。
 */
require_once 'PHP/Object.php';

function p($data) {
    return PHP_Object::factory($data);
}

// 読み込むファイルサイズが大きいため、メモリ設定を変更
p('memory_limit')->ini_set(128 * 1000 * 1024);

// HTMLファイルを読み込む
$html = p('bightml.html')->file_get_contents;
if ($html->val === false) {
    die('Fail to get contents' . "\n"); 
}

$regex = '/(?:<h3 +class="title">\s*説明\s*<\/h3>|'
       . '<p +class="para[^"]*">\s*手続き型[^<>]*<\/p>)\s*'
       . '<div +class="methodsynopsis +dc-description">(.+?)<\/div>'
       . '/imsu';
// 関数の説明部分を取得
if ($html->preg_match_all($regex, &$matches)->val === 0) {
    die('Not matched' . "\n"); 
}


$text_node = '(?:<[^<>]+>\s*)*([^<>]+?)\s*(?:<\/[^<>]+>\s*)*\s*';
$regex1 = '/<span +class="type">\s*' . $text_node . '<\/span>\s*'
        . '<span +class="methodname">\s*' . $text_node . '<\/span>\s*'
        . '\((.+?)\)'
        . '/imsu';
$regex2 = '/<span +class="type">\s*' . $text_node . '<\/span>\s*'
        . '<tt +class="parameter[^"]*">\s*' . $text_node . '<\/tt>'
        . '/imsu';

$csv = p(array());
foreach (p($matches[1]) as $func_html) {
    // 戻り値のタイプ、関数名、引数部分にわける
    if ($func_html->preg_match($regex1, &$match1)->val === 0) {
        continue;
    }
    list(, $type, $name, $args) = $match1;

    // メソッドは含めない
    if (p($name)->preg_match('/::|->/')->val !== 0) {
        continue;
    }

    $params = p(array());
    foreach (p($args)->replace('[,', ',[')->explode(',') as $arg) {
        // 引数のタイプとパラメータを取得
        $arg->preg_match($regex2, &$match2);
        $var = $arg->preg_match('/[\[\]]/')->val ? '[ ' . $match2[2] . ' ]' : $match2[2];
        $params->array_push($match2[1], $var);
    }
    $csv[$name] = $params->tap('unshift', $type, $name)->join(',');
}

// CSVファイルを作成
$csv->tap('ksort')->join("\n")->htmlspecialchars_decode(ENT_QUOTES)->file_put_contents('functions.csv');
