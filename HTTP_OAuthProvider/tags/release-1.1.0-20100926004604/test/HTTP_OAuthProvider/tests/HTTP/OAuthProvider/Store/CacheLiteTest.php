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
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   1.1.0
 * @link      http://openpear.org/package/HTTP_OAuthProvider
 */
require_once 'PHPUnit/Framework.php';
require_once 'HTTP/OAuthProvider.php';
require_once 'HTTP/OAuthProvider/Mock/HMAC_SHA1.php';

/**
 * HTTP_OAuthProvider_Store_CacheLiteTest
 *
 * @category HTTP
 * @package  OAuthProvider
 * @author   Tetsuya Yoshida <tetu@eth0.jp>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  1.1.0
 * @link     http://openpear.org/package/HTTP_OAuthProvider
 */
class HTTP_OAuthProvider_Store_CacheLiteTest extends PHPUnit_Framework_TestCase
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
        $this->store = HTTP_OAuthProvider_Store::factory('CacheLite');
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
     * testProcess
     * 
     * Check process of issuing a token.
     *
     * @return void
     *
     * @test
     */
    public function testProcess()
    {
        // issue request token
        $this->store->issueRequestToken($this->provider);
        $this->assertEquals('request', $this->store->getType());
        $this->assertEquals('testuser', $this->store->getConsumerKey());
        $this->assertNotNull($this->store->getTimestamp());
        $this->assertNotNull($this->store->getToken());
        $this->assertNotNull($this->store->getSecret());
        $this->assertTrue($this->store->save());
        $token = $this->store->getToken();

        // authorize request token
        $user_id = '123456789';
        $this->store->loadToken($this->provider, $token);
        $this->store->authorizeToken($user_id);
        $this->assertEquals('authorize', $this->store->getType());
        $this->assertEquals($token, $this->store->getToken());
        $this->assertEquals($user_id, $this->store->getUserID());
        $this->assertNotNull($this->store->getVerifier());
        $this->assertTrue($this->store->save());

        // exchange access token
        $this->store->loadToken($this->provider, $token);
        $this->store->exchangeAccessToken();
        $this->assertEquals('access', $this->store->getType());
        $this->assertNotEquals($token, $this->store->getToken());
        $this->assertTrue($this->store->save());
    }
}
