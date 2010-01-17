<?php
include_once dirname(__FILE__) . '/t/t.php';

$t = new lime_test;


// とりあえず実行して例外が出なければ良しとする
$text = '* header1
paragraph

|*aaa|*bbb|
|ccc | ddd|

** header2

-a
-b
-+c
-+d
-+-e
-+-f
-----g

:abc:def
::ghi

** header3
[:contents]

>http://google.com>
foo
bar
<<

>>
foo
bar
<<

[http://google.com:title=google]

*** header4

>|
hahaha|<

>|javascript|

(function() {
    return;
})();

*** header5
paragraph((footnote))

||<

';

HatenaSyntax::render($text);