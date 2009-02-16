<?php
require_once 'Service/Ustream/Abstract.php';


class Service_Ustream_User extends Service_Ustream_Abstract
{
    public function getInfo($user)
    {
        $url = sprintf("%s/%s/user/%s/getInfo?key=%s",
                    self::API_URI,
                    $this->getResponseType(),
                    $user,
                    $this->getApiKey()
        );

        $this->_send($url);
        return $this->getResult()->results;
        
    }
	public function getId($user)
    {
        $url = sprintf("%s/%s/user/%s/getId?key=%s",
                    self::API_URI,
                    $this->getResponseType(),
                    $user,
                    $this->getApiKey()
        );

        $this->_send($url);
        return $this->getResult()->results;
    }
    public function getValueOf($user, $key)
    {
        $url = sprintf("%s/%s/user/%s/getValueOf/%s?key=%s",
                    self::API_URI,
                    $this->getResponseType(),
                    $user,
                    $key,
                    $this->getApiKey()
        );

        $this->_send($url);
        return $this->getResult()->results;
    }

    public function listAllChannels($user)
    {
        $url = sprintf("%s/%s/user/%s/listAllChannels?key=%s",
                    self::API_URI,
                    $this->getResponseType(),
                    $user,
                    $this->getApiKey()
        );

        $this->_send($url);
        $this->_result = new Service_Ustream_Result_User($this->_response, $this->getResponseType());
        return $this->getResult()->results;
    }

    public function listAllVideos($user)
    {
        $url = sprintf("%s/%s/user/%s/listAllVideos?key=%s",
                    self::API_URI,
                    $this->getResponseType(),
                    $user,
                    $this->getApiKey()
        );

        $this->_send($url);
        return $this->getResult()->results;
    }

    public function search($command)
    {
        $url = sprintf("%s/%s/user/recent/search/%s?key=%s",
                    self::API_URI,
                    $this->getResponseType(),
                    $command,
                    $this->getApiKey()
                );
        $this->_send($url);
        return $this->getResult()->results;

    }
}