<?php

class Wozozo_WWW_YouTube_VideoInfo implements ArrayAccess
{

    const FORMAT_FLV = 'flv';
    const FORMAT_MP4 = 'mp4';
    const FORMAT_3GP = '3gp';
    const FORMAT_VP8 = 'vp8';

    private $_parsedVideoInfo;

    public function __construct(array $parsedVideoInfo)
    {
        if (!isset($parsedVideoInfo['status'])) {
            $msg = implode('', $parsedVideoInfo);
            throw new UnexpectedValueException("invalid array is passed, array has not 'status' - key");
        }

        $this->_parsedVideoInfo = $parsedVideoInfo;
    }

    public function makeDownloadUrl($preferFmt = null)
    {
        if ($this->_parsedVideoInfo['status'] !== 'ok') {
            if ($this->_parsedVideoInfo['status'] === 'fail') {
                throw new Exception($this->_parsedVideoInfo['reason'], $this->_parsedVideoInfo['errorcode']);
            } else {
                throw new Exception('error raise by unknown status'.$this->_parsedVideoInfo['status']);
            }
        }

        // get detected high quality fmt
        $fmt = $this->detectFmt($preferFmt);

        $fmtUrlMap = $this->parseFmtUrlMap();

        return $fmtUrlMap[$fmt];
    }

    /**
     * detect format int (High)
     *
     * @see http://en.wikipedia.org/wiki/YouTube#Quality_and_codecs
     *
     * @param int|string|null
     * @return int
     */
    public function detectFmt($preferFmt = null)
    {
        if (is_integer($preferFmt)) {
            return $preferFmt;
        }

        $fmts = $this->parseFmtUrlMap();

        if (null === $preferFmt) {
            return key($fmts); //@todo calcurate high fmt
        }

        $keys = array_keys($fmts);

        switch (strtolower($preferFmt)) {
            case self::FORMAT_MP4:
                $fmt = current(array_intersect($keys, array(38, 37, 22, 18)));
                break;
            case self::FORMAT_VP8:
                $fmt = current(array_intersect($keys, array(45, 43)));
                break;
            case self::FORMAT_FLV:
                $fmt = current(array_intersect($keys, array(35, 34, 5)));
                break;
            case self::FORMAT_VP8:
                $fmt = current(array_intersect($keys, array(17)));
                break;
            default:
                throw new Exception('unknown format');
        }

        if (false === $fmt) {
            throw new Exception("couldn't find format");
        }

        return $fmt;
    }

    /**
     * @deprecated
     *
     * Get format list
     *  fmt_list or fmt_map
     *  eg. 22/2000000/9/0/115,35/640000/9/0/115,34/0/9/0/115,5/0/7/0/0"
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
        return http_build_query($this->_parsedVideoInfo, null, '&');
    }

    public function parseFmtUrlMap()
    {
        if (!($this->offsetExists('fmt_url_map'))) {
            throw new RuntimeException('fmt_url_map not exists');
        }

        $fmtUrlMap = $this->offsetGet('fmt_url_map');
        $maps = preg_split('#,#', $fmtUrlMap);

        $ret = array();
        foreach ($maps as $map){
            list($key, $var) = preg_split('#\|#', $map);
            $ret[$key] = $var;
        }
        
        return $ret;
    }

    public function parseFmtMap()
    {
        if (!($this->offsetExists('fmt_map'))) {
            throw new RuntimeException('fmt_map not exists');
        }

        $fmtMap = $this->offsetGet('fmt_map');
        $maps = preg_split('#,#', $fmtMap);

        $ret = array();
        foreach ($maps as $map){
            $m = preg_split('#/#', $map);
            $ret[array_shift($m)] = $m;
        }

        return $ret;
    }
}
