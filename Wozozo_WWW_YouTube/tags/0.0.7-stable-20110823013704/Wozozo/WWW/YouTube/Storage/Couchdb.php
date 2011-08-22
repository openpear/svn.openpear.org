<?php
require_once 'Sopha/Db.php';

class Wozozo_WWW_YouTube_Storage_Couchdb
{
    private $_document;
    private $_url;
    private $_name = 'video';

    public function __construct($videoInfo, $url, $dbname, $host = 'localhost', $port = Sopha_Db::COUCH_PORT)
    {
        $db = new Sopha_Db($dbname, $host, $port);

        $this->_validateExists($url, $db);

        $this->_document = new Sopha_Document($videoInfo->toArray(), $url, $db);
        
        $this->_url = $db->getUrl().urlencode($url).'/'.$this->_name;
    }

    public function setAttachmentName($name)
    {
        $this->_name = $name;
    }

    public function getUrl()
    {
        //return $this->_document->getUrl();
        return $this->_url;
    }

    private function _validateExists($url, $db)
    {
        if ($db->retrieve($url)) {
            throw new Exception($url . 'is already used.');
        }
    }

    //save
    public function callbackUpdate($response, $videoInfo, $config)
    {
        $document = $this->_document;
        $document->save();

        $type = 'video/x-flv';
        $document->setAttachment($this->_name, $type, $response->getRawBody());

        $document->save();
    }

}

