<?php
/**
 *
 *
 */

require_once dirname(__FILE__) . '/Common.php';

/**
 *
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
     */
    public function parse($url = '')
    {
        if ($url == '') {
            $url = $this->url;
        }

        $body = $this->fetch($url);

        return $body;
    }

}

