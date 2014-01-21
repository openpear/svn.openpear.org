<?php

require_once('IO/Bit.php');
require_once('IO/Zlib/Deflate.php');

define ('MOD_ADLER', 65521);

class IO_Zlib {
    var $cmf;
    var $flg;
    var $dictid = null;
    var $deflate = null;
    // fixed huffman table (length, distance)
    function parse($zipdata, $offset = 0) {
        $reader = new IO_Bit();
        $reader->input($zipdata);
        $reader->setOffset($offset, 0);
        /*
         * zlib header info
         */
        $this->cmf = $reader->getUI8();
        $cm = $this->cmf & 0x0f;
        if ($cm != 8) { // CM
            new Exception("unknown compression method=$cm");
        }
        $this->flg = $reader->getUI8();
        if ($this->flg & 0x20) {
            $this->dictid = $reader->getUI32BE();
        }
        /*
         * compression data block
         */
        $deflate = new IO_Zlib_Deflate();
        list($deflate_offset, $dummy) = $reader->getOffset();
        $deflate_length = $deflate->parse($zipdata, $deflate_offset);
        $this->deflate = $deflate;
        $reader->setOffset($offset + $deflate_offset + $deflate_length, 0);
        if ($reader->hasNextData(4)) { // XXX
            $this->adler32 = $reader->getUI32BE();
        } else {
            $this->adler32 = null;
        }
    }

    function dump() {
        /*
         * zlib header info
         */
        $cinfo = $this->cmf >> 4;
        $cm = $this->cmf & 0x0f;
        $flevel = $this->flg >> 6;
        $fdict= ($this->flg >> 5) & 1;
        $fcheck = $this->flg & 0x1f;
        printf("CMF:%02X(CINFO=%d CM=%d) FLG:0x%02X(FLEVEL=%d FDICT=%d FCHECK=%d)\n", $this->cmf, $cinfo, $cm, $this->flg, $flevel, $fdict, $fcheck);
        if (! is_null($this->dictid)) {
            printf("DICTID=0x%08X\n", $this->dictid);
        }
        /*
         *  compression data block
         */
        $this->deflate->dump();
        echo "ADLER32:{$this->adler32}\n";
    }

    /*
     * uncompress method
     */
    function inflate($zipdata) {
        $this->parse($zipdata);        
        return IO_SWF_Deflate($zipdata);
    }

    /*
     * compress method
     * type:0(no compression) only
     */
    function deflate($data) {
        $zlibheader = "\x78\x01";
        $deflatedata = IO_Zlib_Deflate::deflate($data);
        return $zlibheader.$deflatedata.pack('N', self::adler32($data));
    }
    function adler32($data) {
        $a = 1;
        $b = 0;
        $data_length = strlen($data);
        for ($i = 0 ; $i < $data_length ; $i++) {
            $a = ($a + ord($data[$i])) % MOD_ADLER;
            $b  = ($b + $a) % MOD_ADLER;
        }
        return ($b << 16) | $a;
    }
}
