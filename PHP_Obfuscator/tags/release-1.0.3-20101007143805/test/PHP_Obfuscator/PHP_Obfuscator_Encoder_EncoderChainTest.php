<?php
ini_set("include_path", dirname(dirname(dirname(__FILE__))) . PATH_SEPARATOR . ini_get("include_path"));

require_once "PHPUnit/Framework/TestCase.php";
require_once 'PHP/Obfuscator/Encoder/EncoderChain.php';

/**
 * testcase for PHP_Obfuscator_Encoder_EncoderChain
 *
 * @version $Id$
 */
class PHP_Obfuscator_Encoder_EncoderChainTest extends PHPUnit_Framework_TestCase
{
    private $obj;

    public function setup() {
        $this->obj = new PHP_Obfuscator_Encoder_EncoderChain();
    }

    public function testNull() {
        $this->assertEquals('<?php phpinfo();', $this->obj->encode('<?php phpinfo();'));
        $this->assertEquals('<?php phpinfo();', $this->obj->decode('<?php phpinfo();'));
        $this->assertEquals('fread($__f, %d)', $this->obj->decode());
    }

    public function testBase64() {
        include_once 'PHP/Obfuscator/Encoder/Base64Encoder.php';
        $this->obj->add(new PHP_Obfuscator_Encoder_Base64Encoder());
        $this->assertEquals('PD9waHAgcGhwaW5mbygpOw', $this->obj->encode('<?php phpinfo();'));
        $this->assertEquals('base64_decode(fread($__f, %d))', $this->obj->decode());
    }

    public function testGzip() {
        include_once 'PHP/Obfuscator/Encoder/GzipEncoder.php';
        $this->obj->add(new PHP_Obfuscator_Encoder_GzipEncoder());
        $this->assertEquals('s7EvyChQAOLMvLR8DU1rAA==', base64_encode($this->obj->encode('<?php phpinfo();')));
        $this->assertEquals('gzinflate(fread($__f, %d))', $this->obj->decode());
    }

    public function testRot13() {
        include_once 'PHP/Obfuscator/Encoder/Rot13Encoder.php';
        $this->obj->add(new PHP_Obfuscator_Encoder_Rot13Encoder());
        $this->assertEquals('<?cuc cucvasb();', $this->obj->encode('<?php phpinfo();'));
        $this->assertEquals('str_rot13(fread($__f, %d))', $this->obj->decode());
    }

    public function testStrtr() {
        include_once 'PHP/Obfuscator/Encoder/StrtrEncoder.php';
        $this->obj->add(new PHP_Obfuscator_Encoder_StrtrEncoder());
        $this->assertRegexp('#<\?[a-zA-Z0-9+/]{3} [a-zA-Z0-9+/]{7}\\(\\);#', $this->obj->encode('<?php phpinfo();'));
        $this->assertRegexp('#^strtr\\(fread\\(\\$__f, %d\\), \'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789\\+/\', \'[a-zA-Z0-9+/]{64}\'\\)#', $this->obj->decode());
    }

    public function testMultiple() {
        include_once 'PHP/Obfuscator/Encoder/GzipEncoder.php';
        include_once 'PHP/Obfuscator/Encoder/Base64Encoder.php';
        include_once 'PHP/Obfuscator/Encoder/Rot13Encoder.php';
        $this->obj->add(new PHP_Obfuscator_Encoder_GzipEncoder());
        $this->obj->add(new PHP_Obfuscator_Encoder_Base64Encoder());
        $this->obj->add(new PHP_Obfuscator_Encoder_Rot13Encoder());
        $this->assertEquals('f7RilPuDNBYZiYE8QH1eNN', $this->obj->encode('<?php phpinfo();'));
        $this->assertEquals('gzinflate(base64_decode(str_rot13(fread($__f, %d))))', $this->obj->decode());
    }

}
