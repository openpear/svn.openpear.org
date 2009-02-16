<?php
require_once 'Service/Ustream/Abstract.php';
require_once 'HTTP/Request2.php';

class Service_Ustream
{
    const VERSION = '0.1.0';
    private static $_commands = array('Channel', 'User', 'Search', 'Stream', 'System', 'Video');

	public static function factory($command, $apiKey = null, $responseType = 'php')
    {
        if (!in_array(ucwords($command), self::$_commands)) {
            require_once 'Service/Ustream/Exception.php';
            throw new Service_Ustream_Exception('Invalid API command.');
        }
        require_once 'Service/Ustream/' . ucwords($command) . '.php';
        $class = 'Service_Ustream_' . ucwords($command);

        return new $class($apiKey, $responseType);
    }
}