<?php

class Services_Recruit
{
    function &factory($key, $serviceName, $serviceApiVersion = null)
    {
        $file_name = ucfirst($serviceName).'.php';
        $class_name = 'Services_Recruit_'.ucfirst(strtolower($serviceName));

        require_once 'Services/Recruit/'.$file_name;

        $object =& new $class_name($key, $serviceName, $serviceApiVersion);

        return $object;
    }
}

?>
