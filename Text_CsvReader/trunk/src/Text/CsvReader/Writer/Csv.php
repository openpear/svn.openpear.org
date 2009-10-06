<?php
include_once('Stream/Filter/Mbstring.php');
class Text_CsvReader_Writer_Csv extends Text_CsvReader_Writer
{
  protected
    $requiredOptions = array('file'),
    $options = array('charset' => 'UTF-8',
                     'row_size' => 65536,
                     'delimiter' => ',',
                     'enclosure' => '"',
                     'basedir' => null);

  public function __construct($options = array(), $messages = array())
  {
    parent::__construct($options, $messages);
    stream_filter_register("convert.mbstring.*",
                           "Stream_Filter_Mbstring");

  }
  public function initialize()
  {
    $this->fp = $this->fopen();
  }
  public function fopen()
  {
    $file = $this->getOption('file');
    if ($this->hasOption('basedir') && !preg_match('|^/|', $file)) {
      // fileが/以外から始まっている場合、先頭にbasedirを追加する
      $file = $this->getOption('basedir')."/".$file;
    }
    $fp = fopen($file, 'w');
    if ($fp === false) {
      throw new CsvReaderException('failed to open output file: '.$file);
    }
    if ($this->hasOption('charset') && $this->getOption('charset') !== 'UTF-8') {
      $filter_name = sprintf('convert.mbstring.encoding.UTF-8:%s',
                             $this->getOption('charset'));
      $s_filter = stream_filter_append($fp, $filter_name, STREAM_FILTER_WRITE);
      if ($s_filter === false) {
        fclose($fp);
        throw new CsvReaderException('Cannot append stream filter: '. $filter_name);
      }
    }
    return $fp;
  }
  public function fclose()
  {
    if (is_resource($this->fp)) {
      fclose($this->fp);
      $this->fp = null;
    }
  }
  public function write($values) {
    $ret = fputcsv($this->fp,
                   $values,
                   $this->getOption('delimiter'),
                   $this->getOption('enclosure'));
    if ($ret === false) {
      fclose($this->fp);
      throw new CsvReaderException('writing csv failure.');
    }
  }
  public function finalize() {
    $this->fclose();
  }
  public function rollback() {
    $this->fclose();
    // rmすべき？
  }
}
