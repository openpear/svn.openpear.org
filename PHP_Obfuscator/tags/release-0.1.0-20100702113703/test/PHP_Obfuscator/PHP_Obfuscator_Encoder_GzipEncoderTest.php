<?php
ini_set("include_path", dirname(__FILE__)."/../" . PATH_SEPARATOR . ini_get("include_path"));

require_once "PHPUnit/Framework/TestCase.php";
require_once 'PHP/Obfuscator/Encoder/GzipEncoder.php';

/**
 * testcase for PHP_Obfuscator_Encoder_GzipEncoder
 *
 * @version $Id$
 */
class PHP_Obfuscator_Encoder_GzipEncoderTest extends PHPUnit_Framework_TestCase
{
    private $obj;

    public function setup() {
        $this->obj = new PHP_Obfuscator_Encoder_GzipEncoder(array('1', '2'));
        $this->obj->setStr('<?php phpinfo();');
    }

    public function testEncode() {
        $this->assertEquals('s7EvyChQAOLMvLR8DU1rAA==', base64_encode($this->obj->encode()));
    }

    public function testDecode() {
        $this->assertEquals('gzinflate(%s)', $this->obj->decode());
    }
}
