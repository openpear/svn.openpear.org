<?php
require_once 'Service/Ustream/Abstract.php';

class Service_Ustream_Channel extends Service_Ustream_Abstract
{
	public function getInfo($uid)
    {
        $url = sprintf('%s/%s/channel/%s/getInfo?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $this->getApiKey());
        $this->_send($url);
        return $this->getResult()->results;
    }

    public function getValueOf($uid, $key)
    {
        $url = sprintf('%s/%s/channel/%s/getValueOf/%s?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $key,
                    $this->getApiKey());
        $this->_send($url);
        return $this->getResult()->results;
    }

    public function getId($channel)
    {
        return $this->getValueOf($channel, 'id');
        /*
         * Invild COMMAND getId 2009.02.16
        $url = sprintf('%s/%s/channel/%s/getId?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $channel,
                    $this->getApiKey());
        $this->_send($url);echo $url;
        $this->_result = new Service_Ustream_Result_Channel($this->_response, $this->getResponseType());
        return $this->_result->getId();
         */
    }

    public function getEmbedTag($uid)
    {
        $url = sprintf('%s/%s/channel/%s/getEmbedTag?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $this->getApiKey());
        $this->_send($url);
        return $this->getResult()->results;
    }

    /**
     * getCustomEmbedTag
     * 
     * @param string|integer $uid
     * @param array $params
     * @return string
     */
    public function getCustomEmbedTag($uid, $params = array())
    {
        $url = sprintf('%s/%s/channel/%s/getCustomEmbedTag?key=%s%s',
                self::API_URI,
                $this->getResponseType(),
                $uid,
                $this->getApiKey(),
                ((is_array($params)) ? ('&params=' . implode(';', $params)) : null));
         $this->_send($url);
         return $this->getResult()->results;
        
    }

    public function listAllChannels($uid)
    {
        $url = sprintf('%s/%s/channel/%s/listAllChannels?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $this->getApiKey());
        $this->_send($url);
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }

    public function getComments($uid)
    {
        $url = sprintf('%s/%s/channel/%s/getComments?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $this->getApiKey());
        $this->_send($url);
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }

    public function getTags($uid)
    {
        require_once 'Service/Ustream/Exception.php';
        throw new Service_Ustream_Exception('******');
        return;
        $url = sprintf('%s/%s/channel/%s/getTags?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $this->getApiKey());
        $this->_send($url);
        echo $url;
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }
}