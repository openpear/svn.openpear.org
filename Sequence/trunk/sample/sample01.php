<?php
include_once dirname(__FILE__) . '/../src/seq.php';

list($a, $b) = seq(1, 2, 3, 4)->map('min', 2)->pick(0, 3);
// $a => 2
// $b => 4

// これは上の例とおなじ
seq(1, 2, 3, 4)->map('min', 2)->pick(0, -1)->tovar($a, $b);

// メソッドによる要素へのアクセス
seq(4, 5, 6)->nth(0); // => 4
seq(4, 5, 6)->nth(-2); // => 5

// 添え字による要素へのアクセス
$seq = seq(7, 8, 9);
$seq[1]; // => 8

// 要素の追加
$seq[] = 10;
$seq->push(11);

// 当然foreach文にも使える
foreach ($seq as $elt) echo $elt . PHP_EOL;

// 配列に変換
seq(1, 2, 3)->toArray();

// リストの大きさを得る
count($seq);
$seq->count();
$seq->length;

// 大きさを切り詰める
$seq->length = 3;
$seq->lengthen(3);

// ある要素を持っているかどうか
seq(1, 2, 3, 4)->has(4); // => true

// リストを最初の要素と残りのリストとに分割する
list($first, $rest) = seq("a", "b", "c")->unclip();
// $first => "a"
// $rest => seq("b", "c")

// コールバックを受け取るメソッド群
seq("hoge", "", "fuga")->filter('strlen'); // => seq("hoge", "fuga")
seq(1, 2, 3)->reduce(create_function('$a, $b', 'return $a + $b;')); // => 6
seq("a", "b", "c")->all('is_string'); // => true
seq("a", 1, "b", "c")->any('is_int'); // => true
seq(1, 2, 3)->each('var_dump'); 