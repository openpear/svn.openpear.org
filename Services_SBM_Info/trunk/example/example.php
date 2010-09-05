<?php if (PHP_SAPI !== 'cli') echo '<pre>'; ?>
<?php
require_once 'Services/SBM/Info.php';

$SBMInfo = new Services_SBM_Info('http://example.net/', 'Example Web Page');

/**
 * 実行 (APIからデータを取得)
 */
$SBMInfo->execute();

/**
 * 有効サービスの全SBM情報を配列で取得 (コメントを除く)
 */
//print_r($SBMInfo->toArray());

/**
 * 有効サービスの全SBM情報をJSON文字列で取得 (コメントを除く)
 */
//echo $SBMInfo->toJson();

/**
 * 有効サービスのコメントを含む全SBM情報を配列で取得
 */
//print_r($SBMInfo->toArray(true));

/**
 * 有効サービスのコメントを含む全SBM情報をJSON文字列で取得
 */
//echo $SBMInfo->toJson(true);

/**
 * コンストラクタの引数は省略可能
 */
$SBMInfo = new Services_SBM_Info;

/**
 * 新しいURLとページタイトルをセット
 */
$SBMInfo->setUrl('http://example.com/')
        ->setTitle('Example Web Page');

/**
 * 有効サービスをセット
 */
$SBMInfo->setServices(array('hatena', 'delicious', 'livedoor'));
// or
$SBMInfo->setServices('hatena,delicious,livedoor');

/**
 * URLまたは有効サービスをセットした場合はexecuteを呼ぶ
 */
$SBMInfo->execute();

/**
 * 有効サービスにLivedoorを追加したので、
 * 全SBM情報にLivedoor clipの情報が追加される
 */
//print_r($SBMInfo->toArray());

/**
 * SBM情報を個別に取得する
 */
//echo 'Count: '     . $SBMInfo->getCount('hatena')    . PHP_EOL;
//echo 'Unit: '      . $SBMInfo->getUnit('hatena')     . PHP_EOL;
//echo 'Rank: '      . $SBMInfo->getRank('hatena')     . PHP_EOL;
//echo 'Entry URL: ' . $SBMInfo->getEntryUrl('hatena') . PHP_EOL;
//echo 'Add URL: '   . $SBMInfo->getAddUrl('hatena')   . PHP_EOL;
//echo 'Comments: '  . print_r($SBMInfo->getComments('hatena')) . PHP_EOL ;

?>
<?php if (PHP_SAPI !== 'cli') echo '</pre>'; ?>