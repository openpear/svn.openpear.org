<?php
// $Id$

Wozozo_TinyCrypt {
    static public function encode($input) {
        // RFC3548: URL safe Base64 encoding
        static $search = array('+', '/');
        static $replace = array('-', '_');

        $gzed = gzdeflate($input);
        $encoded = base64_encode($gzed);
        $result = str_replace($search, $replace, $encoded);
        // trim trailing '='
        return rtrim($result, '=');
    }

    static public function decode($encoded) {
        static $search = array('-', '_');
        static $replace = array('+', '/');
        $str = str_replace($search, $replace, $encoded);

        $decoded = base64_decode($str);
        return gzinflate($decoded);
    }
}

?>
