<?php
ini_set("include_path", dirname(dirname(dirname(__FILE__))) . PATH_SEPARATOR . ini_get("include_path"));

require_once "PHPUnit/Framework/TestCase.php";
require_once 'PHP/Obfuscator/Encoder/NullEncoder.php';

/**
 * testcase for PHP_Obfuscator_Encoder_NullEncoder
 *
 * @version $Id$
 */
class PHP_Obfuscator_Encoder_NullEncoderTest extends PHPUnit_Framework_TestCase
{
    private $obj;

    public function setup() {
        $this->obj = new PHP_Obfuscator_Encoder_NullEncoder(array('1', '2'));
        $this->obj->setStr('<?php phpinfo();');
    }

    public function testEncode() {
        $this->assertEquals('<?php phpinfo();', $this->obj->encode());
    }

    public function testDecode() {
        $this->assertEquals('%s', $this->obj->decode());
    }
}
