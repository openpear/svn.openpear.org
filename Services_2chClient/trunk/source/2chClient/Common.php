<?php
/**
 * Common.php
 *
 */

/**
 * Services_2chClient_Common
 * 
 */
class Services_2chClient_Common
{
    /**
     * ユーザーエージェント
     */
    protected $_userAgent = 'Monazilla/1.00 (Services_2chClient)';

    /**
     * 最終更新日時
     */
    protected $_lastModified;

    /**
     * HTTP_Request
     */
    protected $http_req;

    /**
     * construct
     *
     */
    public function __construct()
    {
        $this->http_req = new HTTP_Request();
        $this->http_req->addHeader('User-Agent', $this->_userAgent);
    }

    /**
     * setProperty
     *
     */
    public function setProperty($name, $value)
    {
        $name = '_' . $name;
        $this->$name = $value;
    }

    /**
     * getProperty
     *
     */
    public function getProperty($name)
    {
        $name = '_' . $name;
        return $this->$name;
    }
}
