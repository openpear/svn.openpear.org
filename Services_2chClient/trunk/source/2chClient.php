<?php
/**
 * 2chClient.php
 *
 */

require_once 'HTTP/Request.php';

require_once dirname(__FILE__) . '/2chClient/BoardList.php';
require_once dirname(__FILE__) . '/2chClient/Board.php';

/**
 * Services_2chClient
 *
 */
class Services_2chClient
{
    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * factory
     *
     */
    public function factory($type)
    {
        $class_name = 'Services_2chClient_' . $type;

        if (!class_exists($class_name)) {
            throw new Exception("class: {$class_name} not found.");
        }

        $instance = new $class_name();

        return $instance;
    }
}

/*
$two_ch = new Services_2chClient();
$board = $two_ch->factory('Board');
$result = $board->fetchThreadList();
var_dump($result);
 */
