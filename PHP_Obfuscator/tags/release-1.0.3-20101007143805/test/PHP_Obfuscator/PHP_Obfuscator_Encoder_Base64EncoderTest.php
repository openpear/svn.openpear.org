<?php
ini_set("include_path", dirname(dirname(dirname(__FILE__))) . PATH_SEPARATOR . ini_get("include_path"));

require_once "PHPUnit/Framework/TestCase.php";
require_once 'PHP/Obfuscator/Encoder/Base64Encoder.php';

/**
 * testcase for PHP_Obfuscator_Encoder_Base64Encoder
 *
 * @version $Id$
 */
class PHP_Obfuscator_Encoder_Base64EncoderTest extends PHPUnit_Framework_TestCase
{
    private $obj;

    public function setup() {
        $this->obj = new PHP_Obfuscator_Encoder_Base64Encoder(array('1', '2'));
        $this->obj->setStr('<?php phpinfo();');
    }

    public function testEncode() {
        $this->assertEquals('PD9waHAgcGhwaW5mbygpOw', $this->obj->encode());
    }

    public function testDecode() {
        $this->assertEquals('base64_decode(%s)', $this->obj->decode());
    }
}
