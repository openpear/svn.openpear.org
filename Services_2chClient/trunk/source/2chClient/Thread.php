<?php
/**
 * Thread.php
 *
 * @package Services_2chClient
 */

require_once dirname(__FILE__) . '/Common.php';

/**
 * Services_2chClient_Thread
 *
 * @see http://age.s22.xrea.com/talk2ch/ 
 *
 */
class Services_2chClient_Thread extends Services_2chClient_Common
{
    protected $url;

    /**
     * __construct
     *
     * @todo now implement
     */
    public function __construct($url, $board_key, $thread_key)
    {
        parent::__construct($url, $board_key, $thread_key);

        $this->url = 'http://' . $url . '/' . $board_key . '/dat/' . $thread_key . '.dat';
    }

    /**
     * parse
     *
     * とってきて配列で返す
     */
    public function parse($url = '')
    {
        if ($url == '') {
            $url = $this->url;
        }

        $body = $this->fetch($url);

        // @todo 任意に変えれるように
        $body = mb_convert_encoding($body, 'UTF-8', 'Shift-JIS');

        $lines = explode("\n", $body);

        $rows = array();
        foreach ($lines as $line) {
            $rows[] = explode('<>', $line);
        }

        return $rows;
    }

}

