<?php
class CSV_Iterator implements Iterator
{
    private $filePointer;
    private $delimiter;
    private $enclosure;
    private $encoding;
    private $rowCounter;
    private $currentRow;
    private $rowLength;
    private $header;
    private $offset;

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
    public function __construct($file, $encoding = 'utf-8', $delimiter = ',', $enclosure = '"', array $header = array(), $rowlength = null)
    {
        $this->filePointer = fopen($file, 'r');
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->encoding = $encoding;
        $this->setRowLength($rowlength);
        
        $this->header = empty($header) ? $this->readRow() : $header;
        $this->offset = ftell($this->filePointer);
        $this->next();
        $this->rowCounter = 0;
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
        $arg = $this->rowLength ? array($this->filePointer, $this->rowLength) : array($this->filePointer);

        // 囲い込み記号内で改行できるようにするための処理
        // また、マルチバイト関係で安全に処理するために、文字エンコーディングを一旦UTF-8にする
        while (!feof($this->filePointer)) {
            $line .= mb_convert_encoding(call_user_func_array('fgets', $arg), 'utf-8', $this->encoding);
            $itemcnt = preg_match_all('/'.$e.'/u', $line, $dummy);
            if ($itemcnt % 2 == 0) break;
        }
        
        $csv_line = preg_replace('/(?:\r\n|[\r\n])?$/u', $d, trim($line));
        $csv_pattern = '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/u';
        preg_match_all($csv_pattern, $csv_line, $csv_matches);
        $csv_data = $csv_matches[1];

        foreach($csv_data AS &$column){
            $column = mb_convert_encoding(str_replace($e.$e, $e, preg_replace('/^'.$e.'(.*)'.$e.'$/us','$1',$column)), $this->encoding, 'utf-8');
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
}
?>
