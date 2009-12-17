<?php
require_once 'Services/ShortURL/Googl.php';

class Services_ShortURL_Googl_Mock extends Services_ShortURL_Googl
{
    public function getToken($b)
    {
        return $this->generateToken($b);
    }

    public function getC()
    {
        $args = func_get_args();
        return call_user_func_array(array($this, 'c'), $args);
    }

    public function getD($l)
    {
        return $this->d($l);
    }

    public function getE($l)
    {
        return $this->e($l);
    }

    public function getF($l)
    {
        return $this->f($l);
    }
}
