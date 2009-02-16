<?php
require_once 'Service/Ustream/Abstract.php';

class Service_Ustream_Stream extends Service_Ustream_Abstract
{
    /**
     * 
     * @return array
     */
	public function getRecent()
    {
        $url = sprintf('%s/%s/stream/all/getRecent?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $this->getApiKey());
        $this->_send($url);
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }



    /**
     * @return array
     */
    public function getMostViewers()
    {
        $url = sprintf('%s/%s/stream/all/getMostViewers?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $this->getApiKey());
        $this->_send($url);
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }


    /**
     * This command returns all the current live shows
     * that are new where new is defined as newly created by their owners.
     * The current default value for new is any show less than 1 hour old.
     * This default value may be changed by Ustream.TV at any time.
     * The actual rule used to determine newness will be returned
     *  in the 'msg' portion of the results set when you use this command.
     * Note, the user account may or may not be new.
     * @return array
     */
    public function getRandom()
    {
        $url = sprintf('%s/%s/stream/all/getRandom?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $this->getApiKey());
        $this->_send($url);
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }

    public function getAllNew()
    {
        $url = sprintf('%s/%s/stream/all/getAllNew?key=%s',
                    self::API_URI,
                    $this->getResponseType(),
                    $this->getApiKey());
        $this->_send($url);
        if ($this->getResponseType() == 'xml') {
            $results = $this->getResult()->results;
            return $results['array'];
        } else {
            return $this->getResult()->results;
        }
    }
}