<?php

/**
 * Service factory class for Mixi API
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

require_once 'Services/MixiAPI.php';

/**
 * Service factory class for Mixi API
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
final class Services_MixiAPI_Factory {

    /**
     * 'Footprint' API mode
     */
    const API_MODE_FOOTPRINT = 'Footprint';

    /**
     * 'MyMixi' API mode
     */
    const API_MODE_MYMIXI = 'MyMixi';

    /**
     * 'What's New' API mode
     */
    const API_MODE_WHATSNEW = 'WhatsNew';

    /**
     * 'PostDiary' API mode
     */
    const API_MODE_POSTDIARY = 'PostDiary';

    /**
     * 'PhotoAlbum' API mode
     */
    const API_MODE_PHOTOALBUM = 'PhotoAlbum';

    /**
     * 'AlbumList' API mode
     */
    const API_MODE_ALBUMLIST = 'AlbumList';

    /**
     * constructor
     *
     * @return void
     * @access private
     */
    private function __construct() {
    }

    /**
     * create and return an service instance
     *
     * @param  string    $api  the API mode
     * @param  string    $user username
     * @param  string    $pass password
     * @param  string    $id   mixi id
     * @access public
     * @throws InvalidArgumentException throws InvalidArgumentException if errors occur
     * @throws RuntimeException throws RuntimeException if class not found
     * @static
     */
    public static function getInstance($api, $user, $pass, $id) {
        if (is_null($user) || !is_string($user) || $user === '') {
            throw new InvalidArgumentException('user must be string');
        }
        if (is_null($pass) || !is_string($pass) || $pass === '') {
            throw new InvalidArgumentException('pass must be string');
        }
        if (is_null($id) || !is_string($id) || $id === '') {
            throw new InvalidArgumentException('id must be string');
        }

        $const_name = 'Services_MixiAPI_Factory::API_MODE_' . strtoupper($api);
        if (!defined($const_name)) {
            throw new InvalidArgumentException('Invalid API "' . $api . '"');
        }

        require_once 'Services/MixiAPI/' . $api . '.php';
        $classname = 'Services_MixiAPI_' . $api;
        if (class_exists($classname)) {
            return new Services_MixiAPI($user, $pass, $id, new $classname());
        } else {
            throw new RuntimeException('missing the class "' . $classname . '"');
        }
    }
}
