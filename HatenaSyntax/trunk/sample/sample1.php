<?php
set_include_path(dirname(__FILE__) . '/../code' . PATH_SEPARATOR . get_include_path());
include_once 'HatenaSyntax.php';

$p = new HatenaSyntax_Parser;
$result = $p->parse('*���o��

**�����o��

:��`:����
::����2

-���X�g
-+�����t�����X�g
-+�����t�����X�g
-+-���X�g
-+-���X�g
-���X�g

�{���ł�((�r���̓��e))

|*��� |*��  |
|���|1    |
|�݂���|2    |

>>
***���p
�ł���[
<<

>|php|
echo "hogehoge";||<
');

var_dump($result);