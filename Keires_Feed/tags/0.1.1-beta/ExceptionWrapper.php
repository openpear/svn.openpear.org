<?php
/**
  * Keires_Feed
  *
  * Feed Parser like XML_Feed_Parser
  *
  * PHP version 5
  *
  * LICENSE: This source file is subject to version 3.0 of the PHP license
  * that is available through the world-wide-web at the following URI:
  * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
  * the PHP License and are unable to obtain it through the web, please
  * send a note to license@php.net so we can mail you a copy immediately.
  *
  * @category   XML
  * @package    Keires_Feed
  * @author     KOYAMA Tetsuji <koyama@hoge.org>
  * @copyright  2008-2009 KOYAMA Tetsuji
  * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
  * @version    svn: $Id$
  * @link       http://openpear.org/package/Keires_Feed
  * @since      File available since Release 0.1
  */

require_once 'PEAR.php';
require_once 'PEAR/Exception.php';

class Keires_ExceptionWrapper {

    static public function init() {
        static $initialized = false;
        if (!$initialized) {
            PEAR::setErrorHandling(PEAR_ERROR_CALLBACK,
                                   array('Keires_ExceptionWrapper',
                                         'handleError'));
            $initialized = true;
        }
    }

    static public function wrap($obj) {
        if (is_a($obj, 'PEAR')) {
            $obj->setErrorHandling(PEAR_ERROR_CALLBACK,
                                   array('Keires_ExceptionWrapper',
                                         'handleError'));
        }
    }

    static public function handleError($err) {
        throw self::factory($err);
    }

    static public function factory($err) {
        // default exception class
        $class = 'PEAR_Exception';

        return new $class($err->getMessage(), $err->getCode());
    }
}

?>