<?php

/**
 * WSSE authentication class
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy
 * the PHP License and are unable to obtain it through the web,
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Auth
 * @package   Auth_WSSE
 * @author    Hideyuki SHIMOOKA <shimooka@doyouphp.jp>
 * @copyright 2009 Hideyuki SHIMOOKA
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   SVN:$Id$
 * @link      http://pear.php.net/package/Auth_WSSE
 */

/**
 * Auth_WSSE class
 *
 * @category  Auth
 * @package   Auth_WSSE
 * @author    Hideyuki SHIMOOKA <shimooka@doyouphp.jp>
 * @copyright 2009 Hideyuki SHIMOOKA
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Auth_WSSE
 */
class Auth_WSSE
{
    /**
     * user name
     */
    private $username;

    /**
     * password
     */
    private $password;

    /**
     * nonce
     */
    private $nonce;

    /**
     * created time in UTC
     */
    private $created;

    /**
     * constructor
     *
     * @param string user name
     * @param string password
     * @param string nonce without Base64 encode
     * @param string create time (UTC) in RFC3339 format
     */
    public function __construct($username, $password, $nonce = null, $created = null) {
        $this->username = $username;
        $this->password = $password;
        $this->nonce = is_null($nonce) ? $this->generateNonce() : $nonce;
        $this->created = is_null($created) ? $this->generateCreated() : $created;
    }

    /**
     * return the user name
     *
     * @return user name
     */
    public function getUserName() {
        return $this->username;
    }

    /**
     * return the password digest
     *
     * @param  boolean return in Base64 encoded or not. default is true.
     * @return the password digest
     */
    public function getDigest($encode = true) {
        $digest = sha1($this->nonce . $this->created . $this->password, true);
        return $encode ? base64_encode($digest) : $digest;
    }

    /**
     * return the nonce
     *
     * @param  boolean return in Base64 encoded or not. default is true.
     * @return the nonce
     */
    public function getNonce($encode = true) {
        return $encode ? base64_encode($this->nonce) : $this->nonce;
    }

    /**
     * return created date in UTC
     *
     * @return the created time (UTC) in RFC3339 format
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * return X-WSSE header
     *
     * @return the X-WSSE header
     */
    public function getWsseHeader() {
        return sprintf(
            'UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"',
            $this->username,
            $this->getDigest(),
            base64_encode($this->nonce),
            $this->created);
    }

    /**
     * generate nonce
     *
     * @return binary return new nonce
     * @access private
     */
    private function generateNonce() {
        return pack('H*', sha1(md5(microtime() . mt_rand() . uniqid(mt_rand(), true))));
    }

    /**
     * return created time (UTC) in RFC3339 format
     *
     * @return string create time (UTC) in RFC3339 format
     * @access private
     */
    private function generateCreated() {
        return gmdate('Y-m-d\TH:i:s\Z');
    }

}
