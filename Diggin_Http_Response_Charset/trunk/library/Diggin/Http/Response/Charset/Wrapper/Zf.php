<?php

class Diggin_Http_Response_Charset_Wrapper_Zf extends Zend_Http_Response 
{
    /**
     * @var Diggin_Http_Response_Charset_Front_EncodeInterface
     */
    private $_charsetfront;

    /**
     * @var string
     */
    private $_url;

    public function setCharsetFront(Diggin_Http_Response_Charset_Front_EncodeInterface $charsetfront)
    {
        $this->_charsetfront = $chasetfront;
    }

    public function getCharsetFront()
    {
        if (!$this->_charsetfront) {
            require_once 'Diggin/Http/Response/Charset/Front/UrlRegex.php';
            $this->_charsetfront = new Diggin_Http_Response_Charset_Front_UrlRegex;
        }

        return $this->_charsetfront;
    }

    public function getBody()
    {
        $resouce = array('body' => parent::getBody(), 'content-type' => $this->getHeader('content-type'));
        $document = array('url' => $this->getUrl(), 'resouce' => $resouce);
        
        return $this->getCharsetFront()->encode($document);
    }

    public function setUrl($url)
    {
        $this->_url = $url;
    }

    public function getUrl()
    {
        return $this->_url;
    }
}
