<?php

/**
 * Interface for creating/expanding goo.gl links
 *
 * PHP version 5.2.0+
 *
 * LICENSE: This source file is subject to the New BSD license that is
 * available through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/bsd-license.php. If you did not receive
 * a copy of the New BSD License and are unable to obtain it through the web,
 * please send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  CategoryName
 * @package   Services_ShortURL
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2011 Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @license   http://tinyurl.com/new-bsd New BSD License
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/Services_ShortURL
 * @see       http://d.hatena.ne.jp/shimooka/20110112/1294796415
 */

require_once 'Services/ShortURL/Common.php';
require_once 'Services/ShortURL/Interface.php';
require_once 'Services/ShortURL/Exception/CouldNotShorten.php';
require_once 'Services/ShortURL/Exception/CouldNotExpand.php';
require_once 'Services/ShortURL/Exception.php';

/**
 * Interface for creating/expanding goo.gl links
 *
 * @category  CategoryName
 * @package   Services_ShortURL
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2011 Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @license   http://tinyurl.com/new-bsd New BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Services_ShortURL
 * @see       http://d.hatena.ne.jp/shimooka/20110112/1294796415
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
    protected $api = 'https://www.googleapis.com/urlshortener/v1/url';

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
     */
    public function shorten($url)
    {
        $api = $this->api;
        if (isset($options['key']) && $options['key'] !== '') {
            $api .= '?key=' . $options['key'];
        }
        $this->req->setUrl($api);
        $this->req->setMethod(HTTP_Request2::METHOD_POST);
        $this->req->setBody(json_encode(array('longUrl' => $url)));
        $this->req->setHeader('Content-Type', 'application/json');
        $res = $this->req->send();
        if ($res->getStatus() !== 200) {
            throw new Services_ShortURL_Exception_CouldNotShorten(
                'Non-200 code returned', $res->getStatus()
            );
        }

        $data = json_decode($res->getBody());
        return $data->id;
    }

    /**
     * Shorten a URL using {@link http://goo.gl}
     *
     * @param string $url The URL to shorten
     *
     * @throws {@link Services_ShortURL_Exception_CouldNotExpand}
     * @return string The shortened URL
     */
    public function expand($url)
    {
        $this->req->setUrl($this->api . '?shortUrl=' . $url);
        $this->req->setMethod(HTTP_Request2::METHOD_GET);
        $this->req->setBody(null);
        $this->req->setHeader('Content-Type', '');
        $res = $this->req->send();
        if ($res->getStatus() !== 200) {
            throw new Services_ShortURL_Exception_CouldNotExpand(
                'Non-200 code returned', $res->getStatus()
            );
        }

        $json = json_decode($res->getBody());
        if ($json === false || is_null($json)) {
            throw new Services_ShortURL_Exception('failed to decode json');
        } else if ($json->status !== 'OK') {
            throw new Services_ShortURL_Exception('maybe ' . $json->status);
        }

        return $json->longUrl;

    }

}
