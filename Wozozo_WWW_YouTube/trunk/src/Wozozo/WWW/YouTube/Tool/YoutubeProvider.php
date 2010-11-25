<?php
require_once 'Wozozo/WWW/YouTube.php';
require_once 'Zend/Tool/Framework/Provider/Abstract.php';

class Wozozo_WWW_YouTube_Tool_YoutubeProvider extends Zend_Tool_Framework_Provider_Abstract
{
    protected $_specialties = array('SendQueue', 'Couchdb');

    /**
     * @var Wozozo_WWW_YouTube
     */
    protected $_youtube;

    public function getDownloadUrl($id)
    {
        $videoId = Wozozo_WWW_YouTube::detectVideoId($id);
        $youtube = $this->_loadYoutube();
        $url = $youtube->getVideoInfo($videoId)->makeDownloadUrl();
        
        $this->_out($url);
    }

    public function download($id, $path = 'GETCWD')
    {
        $this->_download($id, $path, false);
    }

    public function downloadCouchdb($id)
    {
        $this->_download($id, null, true);
    }

    private function _setupSave($videoInfo, $path, $couch = false)
    {
        if ($couch) {
            $config = $this->_loadConfig('couchdb');
            if (!isset($config->dbname)) {
                throw new Exception ('should config dbname');
            }
            if ($dbname = $config->dbname) {
                require_once 'Wozozo/WWW/YouTube/Storage/Couchdb.php';
                $storage = new Wozozo_WWW_YouTube_Storage_Couchdb($videoInfo, $videoInfo['video_id'], $dbname);
                $this->_youtube->setConfig(array('save' => array($storage, 'callbackUpdate')));

                return $storage->getUrl();
            }
        } else {
            $this->_youtube->setConfig(array('save' => $path));
            $path = $this->_youtube->suggestSavePath($videoInfo);
            if (file_exists($path)) {
                throw new Exception($path.' already exists');
            }

            return $this->_youtube->suggestSavePath($videoInfo);
        }
    }

    protected function _download($id, $save, $couch = false)
    {
        $videoId = Wozozo_WWW_YouTube::detectVideoId($id);
        $this->_out("Video ID :$videoId");

        $youtube = $this->_loadYoutube();
        $videoInfo = $youtube->getVideoInfo($videoId);
        $this->_out("Status :". $videoInfo['status']);

        if ($videoInfo['status'] != 'ok') {
            $videoInfo = $videoInfo->toArray();
            $message = '';
            array_walk($videoInfo, function($v, $k) use (&$message){$message .= $k .' : '. $v .PHP_EOL;});
            throw new Exception("Status is not ok ". PHP_EOL .$message);
        }

        $this->_out("Title : ". $videoInfo['title']);
        $this->_out("Length Seconds : ". $videoInfo['length_seconds']);

        $client = $youtube->getHttpClient();
        //ensure load adapter
        $client->setAdapter('Wozozo_WWW_YouTube_HttpSocketProgressBar');

        $youtube->setHttpClient($client);
        /*
        if ($path) $youtube->setConfig(array('save' => $path)); 
        $path = $youtube->suggestSavePath($videoInfo);
        */
        $path = $this->_setupSave($videoInfo, $save, $couch);

        $this->_out("Downloading ..: ". $path);
        $youtube->downloadByVideoInfo($videoInfo);
    }

    //@todo
    //public function downloadSendQueue()
    //{}

    protected function _loadYoutube()
    {
        if (null === $this->_youtube) {
            $youtube = new Wozozo_WWW_YouTube();
            if ($config = $this->_loadConfig('youtube')) {
                if ($config->httpClient) {
                    require_once 'Zend/Http/Client.php';
                    $client = new Zend_Http_Client(null, $config->httpClient);
                    $youtube->setHttpClient($client);
                }
                $youtube->setConfig($config);
            }

            $this->_youtube = $youtube;
        }

        return $this->_youtube;
    }

    protected function _loadConfig($key)
    {
        $userConfig = $this->_registry->getConfig();

        return $userConfig->$key;
    }

    protected function _out($string)
    {
        $this->_registry->getResponse()->appendContent($string);
    }
}

