<?php

/*
 * 2010/8/11- (c) yoya@awm.jp
 */

require_once 'IO/Bit.php';
require_once dirname(__FILE__).'/SWF/Type.php';
require_once dirname(__FILE__).'/SWF/Tag.php';
require_once dirname(__FILE__).'/SWF/Tag/Shape.php';

class IO_SWF {
    // instance variable
    var $_headers = array(); // protected
    var $_tags = array();    // protected

    function parse($swfdata) {
        $reader = new IO_Bit();
        $reader->input($swfdata);

        /* SWF Header */
        $this->_headers['Signature'] = $reader->getData(3);
        $this->_headers['Version'] = $reader->getUI8();
        $this->_headers['FileLength'] = $reader->getUI32LE();
        if ($this->_headers['Signature']{0} == 'C') {
            // CWS の場合、FileLength の後ろが zlib 圧縮されている
            $uncompressed_data = gzuncompress(substr($swfdata, 8));
            if ($uncompressed_data === false) {
                return false;
            }
            $reader = new IO_Bit();
            $reader->input($uncompressed_data);
        }
        /* SWF Movie Header */
        $this->_headers['FrameSize'] = IO_SWF_Type::parseRECT($reader);
        $reader->byteAlign();
        $this->_headers['FrameRate'] = $reader->getUI16LE();
        $this->_headers['FrameCount'] = $reader->getUI16LE();
        
        /* SWF Tags */
        while (true) {
	    $tag = new IO_SWF_Tag();
	    $tag->parse($reader);
            $this->_tags[] = $tag;
            if ($tag->code == 0) { // END Tag
                break;
            }
        }
        return true;
    }

    function dump() {
        /* SWF Header */
        echo 'Signature: '.$this->_headers['Signature'].PHP_EOL;
        echo 'Version: '.$this->_headers['Version'].PHP_EOL;
        echo 'FileLength: '.$this->_headers['FileLength'].PHP_EOL;
        echo 'FrameSize: '.PHP_EOL;
        echo "\tXmin: ".($this->_headers['FrameSize']['Xmin'] / 20).PHP_EOL;
        echo "\tXmax: ".($this->_headers['FrameSize']['Xmax'] / 20).PHP_EOL;
        echo "\tYmin: ".($this->_headers['FrameSize']['Ymin'] / 20).PHP_EOL;
        echo "\tYmax: ".($this->_headers['FrameSize']['Ymax'] / 20).PHP_EOL;
        echo 'FrameRate: '.($this->_headers['FrameRate'] / 0x100).PHP_EOL;
        echo 'FrameCount: '.$this->_headers['FrameCount'].PHP_EOL;

        /* SWF Tags */
        
        echo 'Tags:'.PHP_EOL;
        foreach ($this->_tags as $tag) {
	    $tag->dump();
        }
    }
    
    function build() {
        $writer_head = new IO_Bit();
        $writer = new IO_Bit();

        /* SWF Header */
        $writer_head->putData($this->_headers['Signature']);
        $writer_head->putUI8($this->_headers['Version']);
        $writer_head->putUI32LE($this->_headers['FileLength']);

        /* SWF Movie Header */
	IO_SWF_Type::buildRECT($writer, $this->_headers['FrameSize']);
        $writer->byteAlign();
        $writer->putUI16LE($this->_headers['FrameRate']);
        $writer->putUI16LE($this->_headers['FrameCount']);
        
        /* SWF Tags */
        foreach ($this->_tags as $tag) {
            $tagData = $tag->build();
	    if ($tagData != false) {
                $writer->putData($tag->build());
	    }
        }
        list($fileLength, $bit_offset_dummy) = $writer->getOffset();
        $fileLength += 8; // swf header
        $this->_headers['FileLength'] = $fileLength;
        $writer_head->setUI32LE($fileLength, 4);
        if ($this->_headers['Signature']{0} == 'C') {
            return $writer_head->output() . gzcompress($writer->output());
        }
        return $writer_head->output().$writer->output();
    }
}
