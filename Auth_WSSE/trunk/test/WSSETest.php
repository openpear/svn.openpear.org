<?php
ini_set('include_path', '..' . DIRECTORY_SEPARATOR . PATH_SEPARATOR . ini_get('include_path'));

require_once 'PHPUnit/Framework.php';
require_once 'Auth/WSSE.php';

/**
 * Test class for Auth_WSSE.
 *
 * PHP version 5
 *
 * @category  Auth
 * @package   Auth_WSSE
 * @author    Hideyuki SHIMOOKA <shimooka@doyouphp.jp>
 * @copyright 2009 Hideyuki SHIMOOKA
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   SVN:$Id$
 * @link      http://openpear.org/package/Auth_WSSE
 */

/**
 * Test class for Auth_WSSE.
 *
 * @category  Auth
 * @package   Auth_WSSE
 * @author    Hideyuki SHIMOOKA <shimooka@doyouphp.jp>
 * @copyright 2009 Hideyuki SHIMOOKA
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   SVN:$Id$
 * @link      http://openpear.org/package/Auth_WSSE
 */
class Auth_WSSETest extends PHPUnit_Framework_TestCase
{
    protected $wsse;

    /**
     * Sets up the fixture
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->now = gmdate('Y-m-d\TH:i:s\Z');
        $this->wsse = new Auth_WSSE('shimooka@doyouphp.jp', 'password');
        $this->wsse_nonce = new Auth_WSSE('shimooka@doyouphp.jp', 'password', 'nonce');
        $this->wsse_created = new Auth_WSSE('shimooka@doyouphp.jp', 'password', null, '2009-03-27T12:34:56Z');
        $this->wsse_full = new Auth_WSSE('shimooka@doyouphp.jp', 'password', 'nonce', '2009-03-27T12:34:56Z');
    }

    /**
     * Tears down the fixture
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    public function testGetUserName() {
        $this->assertEquals('shimooka@doyouphp.jp', $this->wsse->getUserName());
        $this->assertEquals('shimooka@doyouphp.jp', $this->wsse_nonce->getUserName());
        $this->assertEquals('shimooka@doyouphp.jp', $this->wsse_created->getUserName());
        $this->assertEquals('shimooka@doyouphp.jp', $this->wsse_full->getUserName());
    }

    public function testGetDigest() {
        $this->assertEquals(1, preg_match('#^[a-zA-Z0-9/+=]+$#', $this->wsse->getDigest()));
        $this->assertEquals(1, preg_match('#^[a-zA-Z0-9/+=]+$#', $this->wsse_nonce->getDigest()));
        $this->assertEquals(1, preg_match('#^[a-zA-Z0-9/+=]+$#', $this->wsse_created->getDigest()));
        $this->assertEquals(1, preg_match('#^[a-zA-Z0-9/+=]+$#', $this->wsse_full->getDigest()));
    }

    public function testGetNonce() {
        $nonce = $this->wsse->getNonce(false);
        $this->assertEquals(base64_encode($nonce), $this->wsse->getNonce());

        $this->assertEquals('nonce', $this->wsse_nonce->getNonce(false));
        $nonce = $this->wsse_nonce->getNonce(false);
        $this->assertEquals(base64_encode($nonce), $this->wsse_nonce->getNonce());

        $nonce = $this->wsse_created->getNonce(false);
        $this->assertEquals(base64_encode($nonce), $this->wsse_created->getNonce());

        $this->assertEquals('nonce', $this->wsse_full->getNonce(false));
        $nonce = $this->wsse_full->getNonce(false);
        $this->assertEquals(base64_encode($nonce), $this->wsse_full->getNonce());
    }

    public function testGetCreated() {
        $this->assertEquals($this->now, $this->wsse->getCreated());
        $this->assertEquals($this->now, $this->wsse_nonce->getCreated());
        $this->assertEquals('2009-03-27T12:34:56Z', $this->wsse_created->getCreated());
        $this->assertEquals('2009-03-27T12:34:56Z', $this->wsse_full->getCreated());
    }

    public function testGetWSSEHeader() {
        $this->assertEquals('UsernameToken Username="shimooka@doyouphp.jp", PasswordDigest="' . $this->wsse->getDigest() . '", Nonce="' . $this->wsse->getNonce() . '", Created="' . $this->now . '"', $this->wsse->getWsseHeader());
        $this->assertEquals('UsernameToken Username="shimooka@doyouphp.jp", PasswordDigest="' . $this->wsse_nonce->getDigest() . '", Nonce="' . $this->wsse_nonce->getNonce() . '", Created="' . $this->now . '"', $this->wsse_nonce->getWsseHeader());
        $this->assertEquals('UsernameToken Username="shimooka@doyouphp.jp", PasswordDigest="' . $this->wsse_created->getDigest() . '", Nonce="' . $this->wsse_created->getNonce() . '", Created="2009-03-27T12:34:56Z"', $this->wsse_created->getWsseHeader());
        $this->assertEquals('UsernameToken Username="shimooka@doyouphp.jp", PasswordDigest="' . $this->wsse_full->getDigest() . '", Nonce="' . $this->wsse_full->getNonce() . '", Created="2009-03-27T12:34:56Z"', $this->wsse_full->getWsseHeader());
    }

}
