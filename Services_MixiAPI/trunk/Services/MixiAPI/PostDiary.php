<?php

/**
 * Post a diary service class for Mixi API
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
class Services_MixiAPI_PostDiary extends Services_MixiAPI_AbstractAPI {

    /**
     * a Services_MixiAPI_Diary object
     */
    private $diary = null;

    /**
     * the API url
     */
    private $url = null;


    /**
     * return the API url
     *
     * @param  strint $id   mixi id
     * @return string    the API url
     * @access public
     */
    public function getApiUrl($id) {
        return is_null($this->url) ? 'http://mixi.jp/atom/diary/member_id=' . $id : $this->url;
    }

    /**
     * setup HTTP_Request object without WSSE Authorization
     *
     * @param  HTTP_Request $request HTTP_Request object
     * @return void
     * @access protected
     */
    protected function setupRequest(HTTP_Request $request) {
        if (is_null($this->diary)) {
            throw new RuntimeException('Service_MixiAPI_Diary object must be set');
        }
        if ($this->diary->hasImage()) {
            $request->addHeader('Content-Type', 'image/jpeg');
            $request->setBody($this->diary->getImage()->getContentsData());
        } else {
            $request->setBody($this->diary->getContentsData());
        }
        $request->setMethod(HTTP_REQUEST_METHOD_POST);
    }

    /**
     * set a Services_MixiAPI_Diary object
     *
     * @param Services_MixiAPI_Diary object $diary a Services_MixiAPI_Diary object
     * @return void
     */
    public function setDiary($diary) {
        if (is_array($diary) &&
            count($diary) === 1) {
            $diary = $diary[0];
        }
        if (get_class($diary) === 'Services_MixiAPI_Diary') {
            $this->diary = $diary;
        } else {
            throw new InvalidArgumentException('Invalid argument');
        }
    }

    /**
     * return 'location'
     *
     * @return string 'location' string
     */
    public function getLocation()
    {
        return isset($this->headers['location']) ? $this->headers['location'] : null;
    }

    /**
     * set the request url
     *
     * @param string $url th request url
     * @return void
     */
    public function setApiUrl($url)
    {
        $this->url = $url;
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
        /**
         * the order of request
         * 1. image
         * 2. text
         *
         * @see http://creazy.net/2008/07/post_a_mixi_dialy_from_php.html
         */
        if ($this->diary->hasImage()) {
            parent::execute($user, $pass, $id);
            $this->setApiUrl($this->getLocation());
            $this->diary->removeImage();
        }
        return parent::execute($user, $pass, $id);
    }

}
