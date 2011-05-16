<?php

class Services_Shitaraba_Exception extends Exception
{
    /**
    * Creates a new Exception object
    *
    * @param string  $message Exception message
    * @param integer $code    Exception code
    */
    public function __construct($message = null, $code = 0)
    {
        parent::__construct($message, intval($code));
    }
}
