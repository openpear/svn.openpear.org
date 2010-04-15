<?php
require_once 'Wozozo/WWW/YouTube.php';
require_once 'Zend/Tool/Framework/Provider/Abstract.php';
class Wozozo_WWW_YouTube_Tool_YoutubeProvider extends Zend_Tool_Framework_Provider_Abstract
{
    protected $_specialties = array('Download');

    public function echoDownload($id)
    {
        $videoId = Wozozo_WWW_YouTube::detectVideoId($id);
        $youtube = $this->_loadYoutube();
        $url = $youtube->getVideoInfo($videoId)->makeDownloadUrl();
        
        $this->_out($url);
    }

    public function runDownload($id, $path = 'PWD')
    {
        $videoId = Wozozo_WWW_YouTube::detectVideoId($id);
        $this->_out("Video ID :$videoId");

        $youtube = $this->_loadYoutube();
        $videoInfo = $youtube->getVideoInfo($videoId);
        $this->_out("Status :". $videoInfo['status']);
        if ($videoInfo['status'] != 'ok') {
            throw new Exception("Status is not ok". implode(' ', $videoInfo->toArray()));
        }
        $this->_out("Title : ". $videoInfo['title']);
        $this->_out("Length Seconds : ". $videoInfo['length_seconds']);

        $client = $youtube->getHttpClient();
        //ensure load adapter
        $client->setAdapter('Wozozo_WWW_YouTube_HttpSocketProgressBar');

        $youtube->setHttpClient($client);
        if ($path) $youtube->setConfig(array('save' => $path)); 
        $path = $youtube->suggestSavePath($videoInfo);

        $this->_out("Downloading ..: ". $path);
        $youtube->downloadByVideoInfo($videoInfo);
    }

    protected function _loadYoutube()
    {
        $youtube = new Wozozo_WWW_YouTube();
        if ($config = $this->_loadConfig('youtube')) {
            if ($config->httpClient) {
                require_once 'Zend/Http/Client.php';
                $client = new Zend_Http_Client(null, $config->httpClient);
                $youtube->setHttpClient($client);
            }
            $youtube->setConfig($config);
        }

        return $youtube;
    }

    protected function _loadConfig($key)
    {
        return false;

        $userConfig = $this->_registry->getConfig();

        return $userConfig->$key;
    }

    protected function _out($string)
    {
        //if ()
        $this->_registry->getResponse()->appendContent($string);

        //echo $string, PHP_EOL;
    }
}

