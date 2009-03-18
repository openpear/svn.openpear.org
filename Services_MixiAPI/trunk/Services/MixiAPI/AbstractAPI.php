<?php

/**
 * Abstract service class for Mixi API
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy
 * the PHP License and are unable to obtain it through the web,
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Services
 * @package   Services_MixiAPI
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2007 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   0.0.1
 * @link      http://pear.php.net/package/Services_MixiAPI
 * @see       References to other sections (if any)...
 */

require_once 'Services/MixiAPI/API.php';
require_once 'HTTP/Request.php';

/**
 * Abstract service class for Mixi API
 *
 * @category  Services
 * @package   Services_MixiAPI
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2007 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   Release: 0.0.1
 * @link      http://pear.php.net/package/Services_MixiAPI
 * @see       References to other sections (if any)...
 */
abstract class Services_MixiAPI_AbstractAPI implements Services_MixiAPI_API {

    protected $headers = array();

    /**
     * execute service
     *
     * @param  strint $user username
     * @param  strint $pass password
     * @param  strint $id   mixi id
     * @return string fetched result in the xml format
     * @access public
     * @throws Exception Exception description (if any) ...
     */
    public function execute($user, $pass, $id) {
        $request = new HTTP_Request($this->getApiUrl($id));
        $request->addHeader('X-WSSE', $this->buildWSSEAuth($user, $pass));
        $this->setupRequest($request);
        if (PEAR::isError($request->sendRequest())) {
            throw new RuntimeException('request failed : ' . $this->getApiUrl($id));
        }
        $this->headers = $request->getResponseHeader();
        return $request->getResponseBody();
    }

    /**
     * build HTTP header for WSSE Authorization
     *
     * @param  string $user user name
     * @param  string $pass password
     * @return string WSSE HTTP header
     */
    private function buildWSSEAuth($user, $pass) {
        $nonce = pack('H*', sha1(md5(time().rand().uniqid(rand(), true))));
        $created = gmdate('Y-m-d\TH:i:s') . 'Z';
        $digest = base64_encode(pack('H*', sha1($nonce . $created . $pass)));
        $wsse_header = sprintf('UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"', $user, $digest, base64_encode($nonce), $created);
        return $wsse_header;
    }

    /**
     * setup HTTP_Request object without WSSE Authorization
     *
     * @param  HTTP_Request $request HTTP_Request object
     * @return void
     * @access protected
     */
    protected function setupRequest(HTTP_Request $request) {
    }
}
