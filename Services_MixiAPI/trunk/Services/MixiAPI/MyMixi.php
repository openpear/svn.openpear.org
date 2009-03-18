<?php

/**
 * My Mixi service class for Mixi API
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

require_once 'Services/MixiAPI/AbstractAPI.php';

/**
 * My Mixi service class for Mixi API
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
class Services_MixiAPI_MyMixi extends Services_MixiAPI_AbstractAPI {

    private $last_login = null;

    /**
     * return the API url
     *
     * @param  strint $id   mixi id
     * @return string    the API url
     * @access public
     */
    public function getApiUrl($id) {
        $url = 'http://mixi.jp/atom/friends/r=1/member_id=' . $id;
        if (!is_null($this->last_login)) {
            $url .= '?last-login=' . $this->last_login;
        }
        return $url;
    }

    public function setLastLogin($last_login)
    {
        if (is_array($last_login) &&
            count($last_login) === 1) {
            $last_login = $last_login[0];
        }
        if ((int)$last_login !== 60) {
            throw new InvalidArgumentException('last login must be 60');
        }
        $this->last_login = (int)$last_login;
    }
}
