<?php

/**
 * Service interface for Mixi API
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

/**
 * Service interface for Mixi API
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
interface Services_MixiAPI_API {

    /**
     * return the API url
     *
     * @param  strint $id   mixi id
     * @return string the API url
     * @access public
     */
    public function getApiUrl($id);

    /**
     * execute service
     *
     * @param  strint $user username
     * @param  strint $pass password
     * @param  strint $id   mixi id
     * @access public
     */
    public function execute($user, $pass, $id);
}
