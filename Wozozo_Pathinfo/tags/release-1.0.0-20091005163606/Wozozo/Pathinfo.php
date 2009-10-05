<?php
// $Id$

require_once 'Openpear/Util.php';

Openpear_Util::import('array_val');

class Wozozo_Pathinfo_Exception extends Exception {
    const E_INVALID_PATHINFO = 1;
    const E_EMPTY_PATHINFO = 2;
}

class Wozozo_Pathinfo {
    static public function parse($options = null) {
        $pathinfo = array_val($options, 'pathinfo');
        $delimiter = array_val($options, 'delimiter', '/');

        if (empty($pathinfo)) {
            $pathinfo = array_val($_SERVER, 'PATH_INFO');
        }
        if (empty($pathinfo)) {
            throw new Wozozo_Pathinfo_Exception(
                'PATH_INFO is not set',
                Wozozo_Pathinfo_Exception::E_EMPTY_PATHINFO);
        }
        if (substr($pathinfo, 0, 1) === '/') {
            $str = substr($pathinfo, 1);
        } else {
            throw new Wozozo_Pathinfo_Exception(
                'Invalid PATH_INFO',
                Wozozo_Pathinfo_Exception::E_INVALID_PATHINFO);
        }

        $pos = strpos($str, $delimiter);
        if ($pos === false) {
            return array($str);
        }
        return explode($delimiter, $str);
    }
}
?>
