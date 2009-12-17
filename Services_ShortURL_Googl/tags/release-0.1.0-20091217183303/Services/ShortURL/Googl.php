<?php

/**
 * Interface for creating/expanding goo.gl links
 *
 * PHP version 5.2.0+
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy
 * the PHP License and are unable to obtain it through the web,
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  CategoryName
 * @package   Services_ShortURL
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2009 Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/Services_ShortURL
 * @see       http://d.hatena.ne.jp/shimooka/
 */

require_once 'Services/ShortURL/Common.php';
require_once 'Services/ShortURL/Interface.php';
require_once 'Services/ShortURL/Exception/NotImplemented.php';
require_once 'Services/ShortURL/Exception/CouldNotShorten.php';

/**
 * Interface for creating/expanding goo.gl links
 *
 * @category  CategoryName
 * @package   Services_ShortURL
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2009 Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Services_ShortURL
 * @see       http://d.hatena.ne.jp/shimooka/
 */
class Services_ShortURL_Googl
    extends Services_ShortURL_Common
    implements Services_ShortURL_Interface
{
    /**
     * API URL
     *
     * @var string $api The URL for the API
     * @access protected
     */
    protected $api = 'http://goo.gl/api/url';

    /**
     * The user name for API
     *
     * @var    string
     * @access protected
     */
    protected $user = 'toolbar@google.com';

    /**
     * Constructor
     *
     * @param array  $options The service options array
     * @param object $req     The request object
     */
    public function __construct(array $options = array(), HTTP_Request2 $req = null)
    {
        parent::__construct($options, $req);
    }

    /**
     * Shorten a URL using {@link http://goo.gl}
     *
     * @param string $url The URL to shorten
     *
     * @throws {@link Services_ShortURL_Exception_CouldNotShorten}
     * @return string The shortened URL
     * @see    Services_ShortURL_Googl::sendRequest()
     */
    public function shorten($url)
    {
        $data = json_decode($this->sendRequest($url));
        return $data->short_url;
    }

    /**
     * Send a request to {@link http://goo.gl}
     *
     * @param string $url The URL to send the request to
     *
     * @throws {@link Services_ShortURL_Exception_CouldNotShorten}
     * @return string
     */
    protected function sendRequest($url)
    {
        $this->req->setUrl($this->api);
        $this->req->setMethod(HTTP_Request2::METHOD_POST);
        $this->req->addPostParameter('user', $this->user);
        $this->req->addPostParameter('url', $url);
        $this->req->addPostParameter('auth_token', $this->generateToken($url));
        $res = $this->req->send();
        if ($res->getStatus() === 201) {
            return $res->getBody();
        }

        throw new Services_ShortURL_Exception_CouldNotShorten(
            'Non-201 code returned', $res->getStatus()
        );
    }

    /**
     * generate token
     *
     * @param string $b The URL to shorten
     *
     * @return string The token for google authentication
     */
    protected function generateToken($b)
    {
        $i = $this->e($b);
        $i = $i >> 2 & 1073741823;
        $i = $i >> 4 & 67108800 | $i & 63;
        $i = $i >> 4 & 4193280 | $i & 1023;
        $i = $i >> 4 & 245760 | $i & 16383;
        $j = "7";
        $h = $this->f($b);
        $k = ($i >> 2 & 15) << 4 | $h & 15;
        $k |= ($i >> 6 & 15) << 12 | ($h >> 8 & 15) << 8;
        $k |= ($i >> 10 & 15) << 20 | ($h >> 16 & 15) << 16;
        $k |= ($i >> 14 & 15) << 28 | ($h >> 24 & 15) << 24;
        $j .= $this->d($k);
        return $j;
    }

    /**
     * calculate value 'c'
     *
     * @return int value 'c'
     */
    protected function c()
    {
        $l = 0;
        foreach (func_get_args() as $val) {
            $val &= 4294967295;

            /**
             * 32bit signed
             * @see http://github.com/yappo/p5-WWW-Shorten-Google/
             */
            $val += $val > 2147483647 ? -4294967296 :
                        ($val < -2147483647 ? 4294967296 : 0);
            $l   += $val;
            $l   += $l > 2147483647 ? -4294967296 :
                        ($l < -2147483647 ? 4294967296 : 0);
        }
        return $l;
    }

    /**
     * calculate value 'd'
     *
     * @param int $l value 'k'
     *
     * @return int value 'd'
     */
    protected function d($l)
    {
        $l = $l > 0 ? $l : $l + 4294967296;
        $m = "$l";  // must to be string
        $o = 0;
        $n = false;
        for ($p = strlen($m) - 1; $p >= 0; --$p) {
            $q = $m[$p];
            if ($n) {
                $q *= 2;
                $o += floor($q / 10) + $q % 10;
            } else {
                $o += $q;
            }
            $n = !$n;
        }
        $m = $o % 10;
        $o = 0;
        if ($m != 0) {
            $o = 10 - $m;
            if (strlen($l) % 2 == 1) {
                if ($o % 2 == 1) {
                    $o += 9;
                }
                $o /= 2;
            }
        }
        return "$o$l";
    }

    /**
     * calculate value 'e'
     *
     * @param string $l The URL to shorten
     *
     * @return int value 'e'
     */
    protected function e($l)
    {
        $m = 5381;
        for ($o = 0; $o < strlen($l); $o++) {
            $m = $this->c($m << 5, $m, ord($l[$o]));
        }
        return $m;
    }

    /**
     * calculate value 'f'
     *
     * @param string $l The URL to shorten
     *
     * @return int value 'f'
     */
    protected function f($l)
    {
        $m = 0;
        for ($o = 0; $o < strlen($l); $o++) {
            $m = $this->c(ord($l[$o]), $m << 6, $m << 16, -$m);
        }
        return $m;
    }
}
