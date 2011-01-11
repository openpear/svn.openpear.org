<?php
ini_set("include_path", dirname(__FILE__) . "/../../" . PATH_SEPARATOR . ini_get("include_path"));

require_once "PHPUnit/Framework/TestCase.php";
require_once 'Services/ShortURL/Googl.php';

/**
 * testcase for Services_ShortURL_Google
 *
 * @version $Id$
 */
class Services_ShortURL_GoogleTest extends PHPUnit_Framework_TestCase
{
    private $obj;

    public function setup() {
        $this->obj = new Services_ShortURL_Googl();
    }

    public function testShorten() {
        $this->assertEquals('http://goo.gl/fbsS', $this->obj->shorten('http://www.google.com/'));
        $this->assertEquals('http://goo.gl/kJ9E', $this->obj->shorten('http://www.php.net/'));
        $this->assertEquals('http://goo.gl/LJGZ', $this->obj->shorten('http://pear.php.net/'));
        $this->assertEquals('http://goo.gl/BuQj', $this->obj->shorten('http://d.hatena.ne.jp/shimooka/'));
    }

    public function testExpand() {
        $this->assertEquals('http://www.google.com/', $this->obj->expand('http://goo.gl/fbsS'));
        $this->assertEquals('http://www.php.net/', $this->obj->expand('http://goo.gl/kJ9E'));
        $this->assertEquals('http://pear.php.net/', $this->obj->expand('http://goo.gl/LJGZ'));
        $this->assertEquals('http://d.hatena.ne.jp/shimooka/', $this->obj->expand('http://goo.gl/BuQj'));
    }
}
