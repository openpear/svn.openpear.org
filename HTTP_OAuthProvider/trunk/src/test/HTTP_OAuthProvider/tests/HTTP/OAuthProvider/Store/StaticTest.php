<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * OAuth authentication class for service provider.
 *
 * PHP versions 5
 *
 * @category  HTTP
 * @package   OAuthProvider
 * @author    Tetsuya Yoshida <tetu@eth0.jp>
 * @copyright 2010 Tetsuya Yoshida
 * @license   http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   1.1.0
 * @link      http://openpear.org/package/HTTP_OAuthProvider
 */
require_once 'PHPUnit/Framework.php';
require_once 'HTTP/OAuthProvider.php';
require_once 'HTTP/OAuthProvider/Mock/HMAC_SHA1.php';

/**
 * HTTP_OAuthProvider_Store_StaticTest
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version  1.1.0
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider_Store_StaticTest extends PHPUnit_Framework_TestCase
{
    /** 
     * setUp
     * 
     * Set up a mock object.
     *
     * @return void
     */
    public function setUp()
    {
        $this->provider = new HTTP_OAuthProvider_Mock_HMAC_SHA1();
        $row = array(
            'type' => 'request',
            'token' => 'yQa5LVZdRnhVD83ELbxsNOsr30iQ3oFqGtXzlWa0XKpYAXHWAchkd8R5wL1YQfxeKWgr8',
            'secret' => 'Yu50YZ1SrpNy8KmV3yV1R232GSajkzboApKgP1eyWCEVpVPskGfhjvmGtgRGpDxMyh4g',
        );
        $this->store = HTTP_OAuthProvider_Store::factory('Static', $row);
    }

    /** 
     * testInstanceType
     * 
     * Check a instance type.
     *
     * @return void
     *
     * @test
     */
    public function testInstanceType()
    {
        $this->assertType('HTTP_OAuthProvider_Store', $this->store);
    }

    /** 
     * testGetterMethod
     * 
     * Check getter method.
     *
     * @return void
     *
     * @test
     */
    public function testGetterMethod()
    {
        $token = 'yQa5LVZdRnhVD83ELbxsNOsr30iQ3oFqGtXzlWa0XKpYAXHWAchkd8R5wL1YQfxeKWgr8';
        $secret = 'Yu50YZ1SrpNy8KmV3yV1R232GSajkzboApKgP1eyWCEVpVPskGfhjvmGtgRGpDxMyh4g';
        $this->assertEquals($token, $this->store->getToken());
        $this->assertEquals($secret, $this->store->getSecret());
    }
}
