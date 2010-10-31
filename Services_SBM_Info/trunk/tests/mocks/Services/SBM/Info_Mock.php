<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/bootstrap.php';
require_once 'Services/SBM/Info.php';

class Services_SBM_Info_Mock extends Services_SBM_Info
{
    public function __set($name, $value)
    {
        return $this->{$name} = $value;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    public function factory($serviceName)
    {
        return parent::factory($serviceName . '_ mock');
    }
}