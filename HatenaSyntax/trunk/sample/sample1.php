<?php
set_include_path(dirname(__FILE__) . '/../code' . PATH_SEPARATOR . get_include_path());
include_once 'HatenaSyntax.php';

$p = new HatenaSyntax_Parser;
$result = $p->parse('*見出し

**小見出し

:定義:説明
::説明2

-リスト
-+順序付きリスト
-+順序付きリスト
-+-リスト
-+-リスト
-リスト

本文です((脚注の内容))

|*種類 |*数  |
|りんご|1    |
|みかん|2    |

>>
***引用
ですよー
<<

>|php|
echo "hogehoge";||<
');

var_dump($result);