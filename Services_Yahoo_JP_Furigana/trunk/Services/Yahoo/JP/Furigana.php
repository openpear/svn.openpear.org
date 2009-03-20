<?php
/**
 * Furigana dispatcher
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to the New BSD license that is
 * available through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/bsd-license.php. If you did not receive
 * a copy of the New BSD License and are unable to obtain it through the web,
 * please send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Services
 * @package   Services_Yahoo_JP_Furigana
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2009 Hideyuki Shimooka
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 * @version   SVN: $Id: Furigana.php,v 1.1 2008/04/28 15:59:31 tetsuya Exp $
 */

require_once "Services/Yahoo/Exception.php";

/**
 * Furigana dispatcher class
 *
 * This class provides a method to create a concrete instance of one
 * of the supported Furigana types (topics).
 *
 * @category  Services
 * @package   Services_Yahoo_JP_Furigana
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2009 Hideyuki Shimooka
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 * @version   Release: 0.0.1
 */
class Services_Yahoo_JP_Furigana
{
    /**
     * Attempts to return a concrete instance of a Furigana class
     *
     * @param string $type Can be one of topics.
     *
     * @return  object Concrete instance of a Furigana class based on the paramter
     * @throws  Services_Yahoo_Exception
     */
    public function factory($type)
    {
        switch ($type) {
        case 'furigana' :
            include_once 'Services/Yahoo/JP/Furigana/' . $type . '.php';
            $classname = 'Services_Yahoo_JP_Furigana_' . ucfirst($type);
            return new $classname;
        default :
            throw new Services_Yahoo_Exception('Unknown Furigana type ' . $type);
            break;
        }
    }
}
?>
