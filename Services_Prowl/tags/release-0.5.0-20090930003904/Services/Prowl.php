<?php
/**
 * Prowl.php
 *
 * @note http://prowl.weks.net/api.php
 */

/*
$api_key = '';

$prowl = new Services_Prowl($api_key);
$result = $prowl->push('aaaaa');

var_dump($result);
*/

/**
 * Services_Prowl
 *
 */
class Services_Prowl
{
    protected $api_key = null;
    protected $api_url = 'https://prowl.weks.net/publicapi/';
    protected $latest_log = array();

    /**
     * __construct
     *
     */
    public function __construct($api_key = null)
    {
        if (is_string($api_key)) {
            $this->api_key = $api_key;
        }
    }

    /**
     * getLog
     *
     */
    public function getLatestLog()
    {
        return $this->latest_log;
    }

    /**
     * post
     *
     */
    protected function post($url, $data = array())
    {
        $query = http_build_query($data, '', "&");

        $context = array(
            'http' => array(
                'method' => 'POST',
                'header' => implode(
                    "\n",
                    array(
                        'Content-Type: application/x-www-form-urlencoded',
                        'Content-Length:'  . strlen($query)
                    )
                ),
                'content' => $query
            )
        );

        $result = file_get_contents($url, false, stream_context_create($context));

        $xml = simplexml_load_string($result);

        if ($xml->success) {
            $log['code'] = (int)$xml->success['code'];
            $log['remaining'] = (int)$xml->success['remaining'];
            $log['resetdate'] = (int)$xml->success['resetdate'];

            $this->latest_log = $log;
        } else if ($xml->error) {
            throw new Exception($xml->error);
        } else {
            throw new Exception($result);
        }

        return true;
    }

    /**
     * push
     *
     */
    public function push($description, $priority = 0)
    {
        $url = $this->api_url . 'add';

        $priority = (int)$priority;

        $data = array(
            'application' => 'Services_Prowl',
            'event' => 'Event',
            'description' => $description,
            'priority' => $priority,
            'apikey' => $this->api_key,
        );

        return $this->post($url, $data);
    }
}

