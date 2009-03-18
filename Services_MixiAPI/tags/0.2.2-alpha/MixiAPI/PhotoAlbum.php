<?php

/**
 * The photo album service class for Mixi API
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
 * @copyright 2008 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   0.0.1
 * @link      http://pear.php.net/package/Services_MixiAPI
 */

require_once 'Services/MixiAPI/AbstractAPI.php';

/**
 * Post a diary service class for Mixi API
 *
 * @category  Services
 * @package   Services_MixiAPI
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2008 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   Release: 0.0.1
 * @link      http://pear.php.net/package/Services_MixiAPI
 */
class Services_MixiAPI_PhotoAlbum extends Services_MixiAPI_AbstractAPI {

    /**
     * 'post' method
     */
    const METHOD_POST = HTTP_REQUEST_METHOD_POST;

    /**
     * 'list' method
     */
    const METHOD_LIST = HTTP_REQUEST_METHOD_GET;


    /**
     * a Services_MixiAPI_Image object
     */
    private $image = null;

    /**
     * album id
     */
    private $album_id = null;

    /**
     * request method
     */
    private $method = HTTP_REQUEST_METHOD_GET;


    /**
     * return the API url
     *
     * @param  strint $id   mixi id
     * @return string    the API url
     * @access public
     */
    public function getApiUrl($id) {
        return 'http://photo.mixi.jp/atom/r=4/member_id=' . $id . '/album_id=' . $this->album_id;
    }

    /**
     * setup HTTP_Request object without WSSE Authorization
     *
     * @param  HTTP_Request $request HTTP_Request object
     * @return void
     * @access protected
     */
    protected function setupRequest(HTTP_Request $request) {
        if (is_null($this->album_id)) {
            throw new RuntimeException('album id must be set');
        }
        if ($this->method === HTTP_REQUEST_METHOD_POST) {
            if (is_null($this->image)) {
                throw new RuntimeException('Service_MixiAPI_Image object must be set');
            }
            $request->addHeader('Content-Type', 'image/jpeg');
            $request->setBody($this->image->getContentsData());
        }
        $request->setMethod($this->method);
    }

    /**
     * set an image to post
     *
     * @param Services_MixiAPI_Image object $image a Services_MixiAPI_Image object
     * @return void
     */
    public function setImage($image) {
        if (is_array($image) &&
            count($image) === 1) {
            $image = $image[0];
        }
        if (get_class($image) === 'Services_MixiAPI_Image') {
            $this->image = $image;
        } else {
            throw new InvalidArgumentException('Invalid argument');
        }
    }

    /**
     * set an album id
     *
     * @param string $id album id
     * @return void
     */
    public function setAlbumId($id) {
        if (is_array($id) &&
            count($id) === 1) {
            $id = $id[0];
        }
        $this->album_id = $id;
    }

    /**
     * set a method
     *
     * @param string $method a request method
     * @return void
     */
    public function setMethod($method) {
        if (is_array($method) &&
            count($method) === 1) {
            $method = $method[0];
        }
        if ($this->method !== self::METHOD_POST &&
            $this->method !== self::METHOD_LIST) {
            throw new InvalidArgumentException('Invalid argument');
        }
        $this->method = $method;
    }

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
    public function execute($user, $pass, $id)
    {
        return parent::execute($user, $pass, $id);
    }
}
