<?php
/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * http://diggin.musicrider.com/LICENSE
 * 
 * @category   Diggin
 * @package    Diggin_Http
 * @subpackage Response_CharactorEncoding
 * @copyright  2006-2009 sasezaki (http://diggin.musicrider.com)
 * @license    http://diggin.musicrider.com/LICENSE     New BSD License
 */

//is readble ? (borrowd from Zend_Loader::isReadable)
if (!$fh = @fopen('Diggin/Http/Response/Exception.php', 'r', true)) {
    class Diggin_Exception extends Exception{}
    class Diggin_Http_Exception extends Diggin_Exception{}
    class Diggin_Http_Response_Exception extends Diggin_Http_Exception{}
    class Diggin_Http_Response_CharactorEncoding_Exception extends Diggin_Http_Response_Exception{}
} else {
    @fclose($fh);
    /**
     * @see Diggin_Http_Response_Exception
     */
    require_once 'Diggin/Http/Response/Exception.php';
    class Diggin_Http_Response_CharactorEncoding_Exception extends Diggin_Http_Response_Exception
    {}
}

