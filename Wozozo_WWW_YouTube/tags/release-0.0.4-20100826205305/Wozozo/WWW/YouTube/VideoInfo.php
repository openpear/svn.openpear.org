<?php

class Wozozo_WWW_YouTube_VideoInfo implements ArrayAccess
{

    const FORMAT_MP4 = 'mp4';
    const FORMAT_FLV = 'flv';

    private $_parsedVideoInfo;

    public function __construct(array $parsedVideoInfo)
    {
        if (!isset($parsedVideoInfo['status'])) {
            $msg = implode('', $parsedVideoInfo);
            throw new UnexpectedValueException("invalid array is passed, array has not 'status' - key");
        }

        $this->_parsedVideoInfo = $parsedVideoInfo;
    }

    public function makeDownloadUrl($fmt = 18)
    {
        if ($this->_parsedVideoInfo['status'] !== 'ok') {
            if ($this->_parsedVideoInfo['status'] === 'fail') {
                throw new Exception($this->_parsedVideoInfo['reason'], $this->_parsedVideoInfo['errorcode']);
            } else {
                throw new Exception('error raise by unknown status'.$this->_parsedVideoInfo['status']);
            }
        }

        // get detected high quality fmt
        $fmt = (string) $this->_highFmt($fmt);

        $videoId = $this->_parsedVideoInfo['video_id'];
        $token = $this->_parsedVideoInfo['token'];

        return sprintf(Wozozo_WWW_YouTube::PATH_DOWNLOAD, $videoId, $token, $fmt);
    }

    protected function _highFmt($fmt)
    {
        if (is_integer($fmt)) {
            return $fmt;
        }

        //} elseif ($fmt === self::FORMAT_HIGH) {
        $fmts  = $this->getFmts();
        rsort($fmts);
        $mp4s = array(37, 22, 18, 17, 13);
        if (self::FORMAT_MP4 === $fmt) {
            $intersect = array_intersect($fmts, $mp4s);
            return current($intersect);
        } else {
            $diff = array_diff($fmts, $mp4s);
            return current($diff);
        }
    }

    /**
     * Get format list
     *  fmt_list or fmt_map
     *  eg. 22/2000000/9/0/115,35/640000/9/0/115,34/0/9/0/115,5/0/7/0/0"
     *
     * @see http://kenz0.s201.xrea.com/weblog/2008/11/youtube_9.html
     * @see http://creazy.net/2008/12/youtube_video_format_list.html
     *
     * @return array
     */
    public function getFmts()
    {
        if (preg_match_all('#(?:^|,)([\d]+)#', $this->offsetGet('fmt_list'), $m)) {
            return $m[1];
        } else {
            throw new Exception('extract fmt_list error');
        }

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
