<?php
ini_set("include_path", dirname(dirname(dirname(__FILE__))) . PATH_SEPARATOR . ini_get("include_path"));

require_once "PHPUnit/Framework/TestCase.php";
require_once 'PHP/Obfuscator/Encoder/Rot13Encoder.php';

/**
 * testcase for PHP_Obfuscator_Encoder_Rot13Encoder
 *
 * @version $Id$
 */
class PHP_Obfuscator_Encoder_Rot13EncoderTest extends PHPUnit_Framework_TestCase
{
    private $obj;

    public function setup() {
        $this->obj = new PHP_Obfuscator_Encoder_Rot13Encoder(array('1', '2'));
        $this->obj->setStr('<?php phpinfo();');
    }

    public function testEncode() {
        $this->assertEquals('<?cuc cucvasb();', $this->obj->encode());
    }

    public function testDecode() {
        $this->assertEquals('str_rot13(%s)', $this->obj->decode());
    }
}
