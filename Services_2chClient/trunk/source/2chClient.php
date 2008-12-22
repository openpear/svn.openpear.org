<?php
/**
 * 2chClient.php
 *
 * API経由でデータをとるものはServices．
 * これはスクレイピングしてるからScraping_2chClientの方がいい気もするけど
 * SubjectやDATがAPIという考えもできるのなんとも
 */

require_once 'HTTP/Request.php';

require_once dirname(__FILE__) . '/2chClient/BoardList.php';
require_once dirname(__FILE__) . '/2chClient/Board.php';
require_once dirname(__FILE__) . '/2chClient/Thread.php';

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
    public function factory()
    {
        $args = func_get_args();

        $class_name = 'Services_2chClient_' . $args[0];

        if (!class_exists($class_name)) {
            throw new Exception("class: {$class_name} not found.");
        }

        $instance = new $class_name($args[1], $args[2], $args[3]);

        return $instance;
    }
}

/*
$host = 'gimpo.2ch.net';
$board_key = 'namazuplus';
$thread_key = 1226725210;
        
$two_ch = new Services_2chClient();

//$board = $two_ch->factory('Board', $host, $board_key);
//$result = $board->fetchThreadList();
//var_dump($result);

$thread = $two_ch->factory('Thread', $host, $board_key, $thread_key);
$data = $thread->parse();
var_dump($data);
 */
