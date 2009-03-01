<?php
include_once dirname(__FILE__) . '/../test/t/t.php';


$str = '*header1

**header2

:definition term:definition description
::description2

-list1
++fuga2
++hoge3
---list4
---list5
-list6

paragraph((footnote))

|*table header |*table header2 |
|apple         |1              |
|orange        |2              |

>|
hoge
fuga|<


>>
***blockquote header
fuga
<<

>|php|
echo "hogehoge";||<

[http://google.com]
';

echo HatenaSyntax::render($str);
