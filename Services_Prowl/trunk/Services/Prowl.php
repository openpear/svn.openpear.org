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

        return file_get_contents($url, false, stream_context_create($context));
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

