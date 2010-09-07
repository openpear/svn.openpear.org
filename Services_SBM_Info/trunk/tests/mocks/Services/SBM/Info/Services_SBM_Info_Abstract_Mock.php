<?php
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/bootstrap.php';
require_once 'Services/SBM/Info/Abstract.php';

class Services_SBM_Info_Abstract_Mock extends Services_SBM_Info_Abstract
{
    public function fetch($url)
    {
        return null;
    }

    public function extractCount($data)
    {
        return $this->_count;
    }

    public function extractComments($data)
    {
        return array();
    }

    public function __set($name, $value)
    {
        return $this->{$name} = $value;
    }

    public function __get($name)
    {
        return $this->{$name};
    }
}