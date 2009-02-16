<?php
require_once 'Service/Ustream/Abstract.php';

class Service_Ustream_System extends Service_Ustream_Abstract
{
	public function heartBeat()
    {
        $url = sprintf('%s/%s/system/status/heartBeat?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $this->getApiKey());
        $this->_send($url);
        return $this->getResult()->results;
    }

    public function ping()
    {
        $url = sprintf('%s/%s/system/status/ping?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $this->getApiKey());
        $this->_send($url);
        return $this->getResult()->results;
    }
}