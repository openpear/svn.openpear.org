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
//print_r($SBMInfo->getAll());

/**
 * 有効サービスのコメントを含む全SBM情報を配列で取得
 */
//print_r($SBMInfo->getAll(true));

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
$SBMInfo->setServices(array('hatena', 'delicious', 'livedoor', 'buzzurl'));
// or
$SBMInfo->setServices('hatena,delicious,livedoor,buzzurl');

/**
 * URLまたは有効サービスをセットした場合はexecuteを呼ぶ
 */
$SBMInfo->execute();

/**
 * 有効サービスにLivedoorとBuzzurlを追加したので、
 * 全SBM情報にLivedoor clipとBuzzurlの情報が追加される
 */
//print_r($SBMInfo->getAll());

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