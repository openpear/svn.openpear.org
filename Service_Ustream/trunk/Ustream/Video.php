<?php

require_once 'Service/Ustream/Abstract.php';

class Service_Ustream_Video extends Service_Ustream_Abstract
{
	public function getInfo($uid)
    {
        $url = sprintf('%s/%s/video/%s/getInfo?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $this->getApiKey());
        $this->_send($url);
        return $this->getResult()->results;
    }

    public function getValueOf($uid, $key)
    {
        $url = sprintf('%s/%s/video/%s/getValueOf/%s?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $key,
                    $this->getApiKey());
        $this->_send($url);
        return $this->getResult()->results;
    }

    public function getId($videoUrl)
    {
        $url = sprintf('%s/%s/video/%s/getId?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $videoUrl,
                    $this->getApiKey());
        $this->_send($url);
        return $this->getResult()->results;
    }

    public function getEmbedTag($uid)
    {
        $url = sprintf('%s/%s/video/%s/getEmbedTag?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $uid,
                    $this->getApiKey());
        $this->_send($url);
        return $this->getResult()->results;
    }

    public function listAllVideos($uid)
    {
        $url = sprintf('%s/%s/video/%s/listAllVideos?key=%s',
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
        $url = sprintf('%s/%s/video/%s/getComments?key=%s',
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
        $url = sprintf('%s/%s/video/%s/getTags?key=%s',
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