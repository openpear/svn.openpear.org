<?php
ini_set("include_path", dirname(__FILE__)."/../" . PATH_SEPARATOR . ini_get("include_path"));

require_once "PHPUnit/Framework/TestCase.php";
require_once 'PHP/Obfuscator/Encoder/StrtrEncoder.php';

/**
 * testcase for PHP_Obfuscator_Encoder_StrtrEncoder
 *
 * @version $Id$
 */
class PHP_Obfuscator_Encoder_StrtrEncoderTest extends PHPUnit_Framework_TestCase
{
    private $obj;

    public function setup() {
        $this->obj = new PHP_Obfuscator_Encoder_StrtrEncoder(array('1', '2'));
        $this->obj->setStr('<?php phpinfo();');
    }

    public function testEncode() {
        $this->assertRegexp('#<\?[a-zA-Z0-9+/]{3} [a-zA-Z0-9+/]{7}\\(\\);#', $this->obj->encode());
    }

    public function testDecode() {
        $this->assertRegexp('#strtr\\(%s, \'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789\\+/\', \'[a-zA-Z0-9+/]{64}\'\\)#', $this->obj->decode());
    }
}
