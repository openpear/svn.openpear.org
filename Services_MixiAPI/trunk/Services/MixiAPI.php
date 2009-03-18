<?php

/**
 * Web API wrapper for Mixi
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

/**
 * Web API wrapper for Mixi
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
class Services_MixiAPI {

    /**
     * username
     * @var    string
     * @access private
     */
    private $user;

    /**
     * password
     * @var    string
     * @access private
     */
    private $pass;

    /**
     * mixi id
     * @var    string
     * @access private
     */
    private $id;

    /**
     * a service instance
     * @var    object Service_MixiAPI_API
     * @access private
     */
    private $api;

    /**
     * fetched result in the xml format
     * @var    string
     * @access private
     */
    private $result;

    /**
     * constructor
     *
     * @param  strint $user username
     * @param  strint $pass password
     * @param  strint $id   mixi id
     * @param  Services_MixiAPI_API $api  Services_MixiAPI_API object
     * @access public
     */
    public function __construct($user, $pass, $id, Services_MixiAPI_API $api) {
        $this->user = $user;
        $this->pass = $pass;
        $this->id = $id;
        $this->api = $api;
    }

    /**
     * execute your service
     *
     * @return void
     * @access public
     */
    public function execute() {
        $this->result = $this->api->execute($this->user, $this->pass, $this->id);
    }

    /**
     * return fetched result in the xml format
     *
     * @return string fetched result
     * @access public
     */
    public function get() {
        return $this->result;
    }

    public function __call($method, $args)
    {
        if (is_callable(array($this->api, $method))) {
            return call_user_func(array($this->api, $method), $args);
        }

        throw new BadMethodCallException($method . ' method does not exist');
    }
}
