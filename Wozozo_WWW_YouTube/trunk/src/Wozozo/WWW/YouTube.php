<?php
require_once 'Wozozo/WWW/YouTube/VideoInfo.php';

class Wozozo_WWW_YouTube
{
    const PATH_INFO = 'http://www.youtube.com/get_video_info?video_id=%s';
    const BASE_URL = 'http://www.youtube.com/watch?v=';

    /**
     * @var Zend_Http_Client
     */
    protected $_httpClient;

    /**
     * @var array
     */
    protected $_config = array('prefer_fmt' => null,
                               'save' => 'GETCWD', //'GETCWD' will use getcwd();
                               'request_video_stream' => true, //output stream 
                               'response_video_cleanup' => true
                               );
    private $_clientStream;

    public function __construct($config = null)
    {
        if ($config) $this->setConfig($config);
    }

    public function setConfig($config = array())
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();

        } elseif (! is_array($config)) {
            throw new Exception('Array or Zend_Config object expected, got ' . gettype($config));
        }

        foreach ($config as $k => $v) {
            $this->_config[strtolower($k)] = $v;
        }
        
        return $this;
    }

    /**
     *
     * @param string|Zend_Uri $videoId
     * @param array|Zend_Config $config
     * @param $path
     * @return Wozozo_WWW_YouTube_VideoInfo
     */
    public static function download($videoId, $config = array())
    {
        $videoId = self::detectVideoId($videoId);

        $self = new self($config);

        $videoInfo = $self->getVideoInfo($videoId);
        $self->downloadByVideoInfo($videoInfo);
        
        return $videoInfo;
    }

    protected function _putVideo($response, Wozozo_WWW_YouTube_VideoInfo $videoInfo, $config)
    {

        if (is_string($config['save'])) {
            $path = $this->suggestSavePath($videoInfo);
        } else {

            return call_user_func($config['save'], $response, $videoInfo, $config);
        }

        $ret = @file_put_contents($path, $response->getRawBody());
        if ($ret === false) {
            throw new Exception('cannot write at' . $path);
        }
    }

    /**
     * request & get videoinfo
     *
     * @param string $videoId
     */
    public function getVideoInfo($videoId)
    {
        $client = $this->getHttpClient();

        $client->setUri(sprintf(self::PATH_INFO, $videoId));
        $response = $client->request();

        parse_str($response->getBody(), $parse);

        return new Wozozo_WWW_YouTube_VideoInfo($parse);
    }

    /**
     * Request video file
     *
     * @param Wozozo_WWW_YouTube_VideoInfo
     * @return Zend_Http_Response_Stream
     */
    public function requestVideo(Wozozo_WWW_YouTube_VideoInfo $videoInfo)
    {   
        // retrive url & save-file-path
        $url = $videoInfo->makeDownloadUrl($this->_config['prefer_fmt']);

        $client = $this->getHttpClient();
        $client->setUri($url);
        
        try {
            $this->_setupClientStream();
            $response = $client->request();
            $this->_restoreClientStream();
            $response->setCleanup($this->_config['response_video_cleanup']);

            return $response;
        } catch (Exception $e) {
            // ensure, restore HttpClient's origin stream
            $this->_restoreClientStream();
            throw $e;
        }
    }

    public function downloadByVideoInfo(Wozozo_WWW_YouTube_VideoInfo $videoInfo)
    {
        $response = $this->requestVideo($videoInfo);
        
        $this->_putVideo($response, $videoInfo, $this->_config);
    }

    private function _setupClientStream()
    {
        $this->_clientStream = $this->getHttpClient()->getStream();
        $this->getHttpClient()->setStream($this->_config['request_video_stream']);
    }
    
    private function _restoreClientStream()
    {
        $this->getHttpClient()->setStream($this->_clientStream);
    }
 
    public function getHttpClient()
    {
        if (!$this->_httpClient) {
            require_once 'Zend/Http/Client.php';
            $this->_httpClient = new Zend_Http_Client(null, array('useragent' => __CLASS__));
        }

        return $this->_httpClient;
    }

    public function setHttpClient(Zend_Http_Client $client)
    {
        $this->_httpClient = $client;
    }

    public function suggestSavePath($videoInfo)
    {
        $dir = $this->_config['save'];
        $fmt = $this->_config['prefer_fmt'];
        if ('GETCWD' ===  $dir) {
            $dir = getcwd();
        } else {
            if(!is_dir($dir)) {
                throw new InvalidArgumentException('Invalid dir'.$dir);
            }
        }
        $path = $dir . DIRECTORY_SEPARATOR . $videoInfo['video_id'] . self::detectSuffix($videoInfo->detectFmt($fmt));
        
        return $path;
    }
    
    /**
     * borrowed from WWW::YouTube::Download
     *
     * @see 
     * http://cpansearch.perl.org/src/XAICRON/WWW-YouTube-Download-0.13/lib/WWW/YouTube/Download.pm
     *
     * @param string 
     * @return string  
     */
    public static function detectSuffix($fmt)
    {
        switch ($fmt) {
            case '18' :
            case '22' :
            case '37' :
            case '38' :
                return '.mp4';
            case '17' :
                return '.3gp';
            case '43':
            case '45':
                return '.vp8';
            default :
                return '.flv';
        }
    }

    /**
     * detect videoId
     *
     * @param string $var (url)
     * @return string|false
     */
    public static function detectVideoId($var)
    {
        if (is_string($var)) {
            if (!preg_match('#^h*(?:ttp\:\/\/)(.+\/watch\?v=.*)#', $var, $match)) {
                if (!preg_match('#^[A-Za-z0-9]+$#', $var)) {
                    throw new InvalidArgumentException('Invalid id '.$var);
                }
                return trim($var);
            }
            //uri
            require_once 'Zend/Uri.php';
            $var = Zend_Uri::factory('http://'.$match[1]);
        }

        if ($var instanceof Zend_Uri_Http) {
            $query = $var->getQueryAsArray();
            return $query['v'];
        } 
        
        return false;
    }
}

