<?php

class Wozozo_WWW_YouTube_VideoInfo implements ArrayAccess
{
    private $_parsedVideoInfo;

    public function __construct(array $parsedVideoInfo)
    {
        if (!isset($parsedVideoInfo['status'])) {
            $msg = implode('', $parsedVideoInfo);
            throw new UnexpectedValueException("invalid array is passed, array has not 'status' - key");
        }

        $this->_parsedVideoInfo = $parsedVideoInfo;
    }

    public function makeDownloadUrl($fmt = '18')
    {
        if ($this->_parsedVideoInfo['status'] !== 'ok') {
            if ($this->_parsedVideoInfo['status'] === 'fail') {
                throw new Exception($this->_parsedVideoInfo['reason'], $this->_parsedVideoInfo['errorcode']);
            } else {
                throw new Exception('error raise by unknown status'.$this->_parsedVideoInfo['status']);
            }
        }

        $videoId = $this->_parsedVideoInfo['video_id'];
        $token = $this->_parsedVideoInfo['token'];

        return sprintf(Wozozo_WWW_YouTube::PATH_DOWNLOAD, $videoId, $token, $fmt);
    }

    public function offsetExists($offset)
    {
        return isset($this->_parsedVideoInfo[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->_parsedVideoInfo[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->_parsedVideoInfo[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->_parsedVideoInfo[$offset]);
    }

    public function toArray()
    {
        return $this->_parsedVideoInfo;
    }

    public function __toString()
    {
        return http_build_query($this->_parsedVideoInfo);
    }

}
