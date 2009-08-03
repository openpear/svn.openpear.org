<?php
class CSV_Iterator implements Iterator
{
    private $filePointer;
    private $delimiter;
    private $enclosure;
    private $encoding;
    private $eol;
    private $rowCounter;
    private $currentRow;
    private $rowLength;
    private $header;
    private $offset;
    private $outputEncoding;

    /**
     * construct
     *
     * @param resource $file
     * @param string $encoding
     * @param string $delimiter
     * @param string $enclosure
     * @param array $header
     * @param integer $rowlength
     */
    public function __construct($file, $encoding = 'utf-8', $delimiter = ',', $enclosure = '"', array $header = array(), $rowlength = null, $outputEncoding = null)
    {
    // XXX: 変換元のエンコードってどう指定するべきかわからないから PHPに任せてます ASCII文字以外渡すとコケる可能性が大
        $this->delimiter = mb_convert_encoding($delimiter, 'utf-8');
        $this->enclosure = mb_convert_encoding($enclosure, 'utf-8');

        $this->setRowLength($rowlength);
        $this->filePointer = fopen($file, 'r');
        $this->_setEncoding($encoding);
        $this->setOutputEncoding($outputEncoding);

        $this->header = empty($header) ? $this->readRow() : $header;
        $this->offset = ftell($this->filePointer);
        $this->rewind();
    }

    public function rewind()
    {
        fseek($this->filePointer, $this->offset);
        $this->next();
        $this->rowCounter = 0;
    }

    public function next()
    {

        $data = $this->readRow();
        if($data!==null && count($data)!==count($this->header)) {
            throw new RuntimeException('CSV parse error. Number of columns is not equal to number of header.');
        }
        $this->currentRow = $data ? array_combine($this->header, $data) : null;
        ++$this->rowCounter;
    }

    public function current()
    {
        return $this->currentRow;
    }

    public function key()
    {
        return $this->rowCounter;
    }

    public function valid()
    {
        return is_array($this->currentRow);
    }

    /**
     * read 1 row of csv.
     *
     * this is a port of the original code written by yossy.
     *
     * @author yossy
     * @author MugeSo
     *
     * @see http://yossy.iimp.jp/wp/?p=56
     * @return array
     */
    private function readRow()
    {
        $d = preg_quote($this->delimiter);
        $e = preg_quote($this->enclosure);
        $line = "";
        //        $arg = $this->rowLength ? array($this->filePointer, $this->rowLength) : array($this->filePointer);
        $arg = array($this->filePointer, $this->rowLength, $this->eol);

        //*
        // 囲い込み記号内で改行できるようにするための処理
        // また、マルチバイト関係で安全に処理するために、文字エンコーディングを一旦UTF-8にする
        while (!feof($this->filePointer)) {
        //            $line .= mb_convert_encoding(call_user_func_array('fgets', $arg), 'utf-8', $this->encoding);
            $line .= mb_convert_encoding(call_user_func_array('stream_get_line', $arg), 'utf-8', $this->encoding);
            $itemcnt = preg_match_all('/'.$e.'/u', $line, $dummy);
            if ($itemcnt % 2 == 0) break;
            $line .= "\xd\xa";
        }

        $csv_line = preg_replace('/(?:\r\n|[\r\n])?$/u', $d, trim($line));
        $csv_pattern = '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/u';
        preg_match_all($csv_pattern, $csv_line, $csv_matches);
        $csv_data = $csv_matches[1];

        if($this->outputEncoding) {
            foreach($csv_data AS &$column){
                $column = mb_convert_encoding(str_replace($e.$e, $e, preg_replace('/^'.$e.'(.*)'.$e.'$/us','$1',$column)), $this->outputEncoding, 'utf-8');
            }
        } else {
            foreach($csv_data AS &$column){
                $column =str_replace($e.$e, $e, preg_replace('/^'.$e.'(.*)'.$e.'$/us','$1',$column));
            }
        }

        return empty($line) ? null : $csv_data;
    }

    /**
     * set row length.
     *
     * don't use usaly.
     *
     * @param <type> $length
     */
    public function setRowLength($length)
    {
        if(!is_int($length) && $length!==null) throw new UnexpectedValueException('argument #1 should be integer or null.');
        $this->rowLength = $length;
    }

    private function _setEncoding($encoding)
    {
        rewind($this->filePointer);
        $this->eol = "\xd\xa";
        if(strtoupper($encoding)==='UTF-16') {
            $bom = fread($this->filePointer, 2);
            if($bom==="\xff\xfe") {
                $encoding = 'UTF-16LE';
                $this->eol = "\xd\x0\xa\x0";
            } elseif($bom==="\xfe\xff") {
                $encoding = 'UTF-16BE';
                $this->eol = "\x0\x0d\x0\x0a";
            } else {
            // 最初の2バイトがBOMでないので巻き戻す
            // ビッグエンディアン
                rewind($this->filePointer);
                $this->eol = "\x0\x0d\x0\x0a";
            }
        }
        $this->encoding = $encoding;
    }

    public function setOutputEncoding($encoding)
    {
        $this->outputEncoding = strtoupper($encoding)==='UTF-8' ? null : $encoding;
    }
}
?>
