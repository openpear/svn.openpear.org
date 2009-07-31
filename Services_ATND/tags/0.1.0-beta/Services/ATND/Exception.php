<?php
class Services_ATND_Exception extends Exception {
    public function __construct($message, $code = 0,  $ch = null) {
        if ($ch !== null) curl_close($ch);
        parent::__construct($message, $code);
    }
}