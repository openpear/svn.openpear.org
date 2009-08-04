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
     * @param string $file CSV file to iterate
     * @param string $encoding File encoding
     * @param string $delimiter The field delimiter. default is ','.
     * @param string $enclosure The field enclosure. default is '"'.
     * @param array $header An array containing header fileds. The first line will be used as header if empty or not supplied.
     * @param integer $rowlength The max length of each line. default is NULL which menas inifinity.
     */
    public function __construct($file, $encoding = 'utf-8', $delimiter = ',', $enclosure = '"', array $header = array(), $rowlength = null, $outputEncoding = null)
    {
    // XXX: 変換元のエンコードってどう指定するべきかわからないから PHPに任せてます ASCII文字以外渡すとコケる可能性が大
        $this->delimiter = mb_convert_encoding($delimiter, 'utf-8');
        $this->enclosure = mb_convert_encoding($enclosure, 'utf-8');

        $this->setRowLength($rowlength);
        $this->filePointer = fopen($file, 'rb');
        $this->_setEncoding($encoding);
        $this->setOutputEncoding($outputEncoding);

        $this->header = empty($header) ? $this->readRow() : $header;
        $this->offset = ftell($this->filePointer);
        $this->rewind();
    }

    public function rewind()
    {
        fseek($this->filePointer, $this->offset);
        $this->rowCounter = -1;
        $this->next();
    }

    public function next()
    {
        ++$this->rowCounter;
        $data = $this->readRow();
        if($data!==null && count($data)!==count($this->header)) {
            throw new RuntimeException('CSV parse error. Number of columns is not equal to number of header at row #'.  $this->rowCounter .'.');
        }
        $this->currentRow = $data ? array_combine($this->header, $data) : null;
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

        //*
        // 囲い込み記号内で改行できるようにするための処理
        // また、マルチバイト関係で安全に処理するために、文字エンコーディングを一旦UTF-8にする
        while (!feof($this->filePointer)) {
            $line .= mb_convert_encoding(self::getLine($this->filePointer, $this->rowLength, $this->eol), 'utf-8', $this->encoding);
            $itemcnt = preg_match_all('/'.$e.'/u', $line, $dummy);
            if ($itemcnt % 2 == 0) break;
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

    /**
     * stream_get_lineにバグがあるため仕方なく作った
     *
     * @see http://bugs.php.net/bug.php?id=49148
     * @param resource $fp ファイルポインタ
     * @param int $buf_size 最大サイズ
     * @param string $eol 行区切り
     * @return string 1行分の文字
     */
    static public function getLine($fp, $buf_size, $eol)
    {
        $ret = '';
        $eol_len = strlen($eol);
        $eol_pos = 0;
        if(!$buf_size) $buf_size = PHP_INT_MAX;
        while(($c = fgetc($fp))!==false && strlen($ret) < $buf_size) {
            $ret .= $c;
            if($c === $eol[$eol_pos]) {
                if(++$eol_pos === $eol_len) {
                    break;
                }
                continue;
            }
            $eol_pos = 0;
        }
        return $ret;
    }
}
?>
