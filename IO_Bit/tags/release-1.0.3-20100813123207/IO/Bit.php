<?php

  /*
   * 2010/07/28- (c) yoya@awm.jp
   */

class IO_Bit {
    /*
     * instance variable
     */
    var $_data;
    var $_byte_offset;
    var $_bit_offset;

    /*
     * data i/o method
     */
    function input($data) {
        $this->_data = $data;
        $this->_byte_offset = 0;
        $this->_bit_offset = 0;
    }
    function output() {
        $output_len = $this->_byte_offset;
        if ($this->_bit_offset > 0) {
            $output_len++;
        }
        if (strlen($this->_data) == $output_len) {
            return $this->_data;
        }
        return substr($this->_data, 0, $output_len);
    }

    /*
     * offset method
     */
    function setOffset($byte_offset, $bit_offset) {
        $this->_byte_offset = $byte_offset;
        $this->_bit_offset  = $bit_offset;
        return true;
    }
    function getOffset() {
        return array($this->_byte_offset, $this->_bit_offset); 
    }
    function byteAlign() {
        if ($this->_bit_offset > 0) {
            $this->_byte_offset ++;
            $this->_bit_offset = 0;
        }
    }
    
    /*
     * get method
     */
    function getData($length) {
        $this->byteAlign();
        $data = substr($this->_data, $this->_byte_offset, $length);
        $data_len = strlen($data);
        $this->_byte_offset += $data_len;
        return $data;
    }
    function getUI8() {
        $this->byteAlign();
        $value = ord($this->_data{$this->_byte_offset});
        $this->_byte_offset += 1;
        return $value;
    }
    function getUI16LE() {
        $this->byteAlign();
        $ret = unpack('v', substr($this->_data, $this->_byte_offset, 2));
        $this->_byte_offset += 2;
        return $ret[1];
    }
    function getUI32LE() {
        $this->byteAlign();
        $ret = unpack('V', substr($this->_data, $this->_byte_offset, 4));
        $this->_byte_offset += 4;
        return $ret[1];
    }
    function getUIBit() {
        $value = ord($this->_data{$this->_byte_offset});
        $value = 1 & ($value >> (7 - $this->_bit_offset));
        $this->_bit_offset ++;
        if (8 <= $this->_bit_offset) {
            $this->_byte_offset++;
            $this->_bit_offset = 0;
        }
        return $value;
    }
    function getUIBits($width) {
        $value = 0;
        for ($i = 0 ; $i < $width ; $i++) {
            $value <<= 1;
            $value |= $this->getUIBit();
        }
        return $value;
    }
    
    /*
     * put method
     */
    function putData($data) {
        $this->byteAlign();
        $this->_data .= $data;
        $this->_byte_offset += strlen($data);
        return true;
    }
    function putUI8($value) {
        $this->byteAlign();
        $this->_data .= chr($value);
        $this->_byte_offset += 1;
        return true;
    }
    function putUI16LE($value) {
        $this->byteAlign();
        $this->_data .= pack('v', $value);
        $this->_byte_offset += 2;
        return true;
    }
    function putUI32LE($value) {
        $this->byteAlign();
        $this->_data .= pack('V', $value);
        $this->_byte_offset += 4;
        return true;
    }
    function _allocData($need_data_len = null) {
        if (is_null($need_data_len)) {
            $need_data_len = $this->_byte_offset;
        }
        $data_len = strlen($this->_data);
        if ($data_len < $need_data_len) {
            $this->_data .= str_pad(chr(0), $need_data_len - $data_len);
        }
        return true;
    }
    function putUIBit($bit) {
        $this->_allocData($this->_byte_offset + 1);
        if ($bit > 0) {
            $value = ord($this->_data{$this->_byte_offset});
            $value |= 1 << (7 - $this->_bit_offset);
            $this->_data{$this->_byte_offset} = chr($value);
        }
        $this->_bit_offset += 1;
        if (8 <= $this->_bit_offset) {
            $this->_byte_offset += 1;
            $this->_bit_offset  = 0;
        }
        return true;
    }
    function putUIBits($value, $width) {
        for ($i = $width - 1 ; $i >= 0 ; $i--) {
            $bit = ($value >> $i) & 1;
            $ret = $this->putUIBit($bit);
            if ($ret !== true) {
                return $ret;
            }
        }
        return true;
    }

    /*
     * set method
     */
    function setUI32LE($value, $byte_offset) {
        $data = pack('V', $value);
        $this->_data{$byte_offset + 0} = $data{0};
        $this->_data{$byte_offset + 1} = $data{1};
        $this->_data{$byte_offset + 2} = $data{2};
        $this->_data{$byte_offset + 3} = $data{3};
        return true;
    }
}
