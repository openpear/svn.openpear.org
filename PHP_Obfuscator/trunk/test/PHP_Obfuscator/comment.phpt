--TEST--
php-obfuscator -f target.php -e Gzip -e Base64 -e Rot13 -c "this is a comment"
--FILE--
<?php
ini_set("include_path", dirname(dirname(dirname(__FILE__))) . PATH_SEPARATOR . ini_get("include_path"));
require_once 'PHP/Obfuscator/Command.php';
PHP_Obfuscator_Command::main(array(dirname(__FILE__) . '/file.php',
                             '-f', dirname(__FILE__) . '/target.php',
                             '-e', 'Gzip',
                             '-e', 'Base64',
                             '-e', 'Rot13',
                             '-c', 'this is a comment',
                             ));
--EXPECTF--
<?php /* this is a comment */$l1ll=count_chars("",4);$lll1=$l1ll[101].$l1ll[118].$l1ll[97].$l1ll[108];$ll1l=$l1ll[98].$l1ll[97].$l1ll[115].$l1ll[101].$l1ll[54].$l1ll[52].$l1ll[95].$l1ll[100].$l1ll[101].$l1ll[99].$l1ll[111].$l1ll[100].$l1ll[101];$l11l=__FILE__;eval($ll1l("JF9fZj1mb3BlbigkbDExbCwicmIiKTtmZ2V0cygkX19mKTtldmFsKGd6aW5mbGF0ZShiYXNlNjRfZGVjb2RlKHN0cl9yb3QxMyhmcmVhZCgkX19mLCA3NCkpKSkpOw"));return; ?>
f7sYlR9COJSrYui7tbjPKd7H5Vk8uMGRxyDA9Hw9KC0HOD+eGXgvqH0SCLHNw4O4I38sn14hNN
