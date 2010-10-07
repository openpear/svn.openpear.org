--TEST--
php-obfuscator --help
--FILE--
<?php
ini_set("include_path", dirname(dirname(dirname(__FILE__))) . PATH_SEPARATOR . ini_get("include_path"));
require_once 'PHP/Obfuscator/Command.php';
PHP_Obfuscator_Command::main(array(dirname(__FILE__) . '/help.php', '--help'));
--EXPECTF--
obfuscate php script.

Usage:
  /home/shimooka/public_html/pear/openpear.org/PHP_Obfuscator/trunk/test/PHP_Obfuscator/help.php
  [options]

Options:
  --verbose                      turn on verbose output
  -t filter, --filter=filter     a list of filters and parameters. specify
                                 'XXXX:args' if use
                                 PHP_Obfuscator_Filter_XXXXFilter
  -e encoder, --encoder=encoder  encoder names. specify 'XXXX' if use
                                 PHP_Obfuscator_Encoder_XXXXEncoder
  -c comment, --comment=comment  comment string
  -f file, --file=file           the script file name to obfuscate. if not
                                 assigned, use stdin.
  -h, --help                     show this help message and exit
  -v, --version                  show the program version and exit
