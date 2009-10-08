<?php
include_once('Stream/Filter/Mbstring.php');
class Text_CsvReader_Reader_Csv extends Text_CsvReader_Reader
{
  protected
    $fp = null,
    $is_eof = true,
    $lineNumber = 0,
    $currentValues = array(),
    $requiredOptions = array('file'),
    $options = array('charset' => 'UTF-8',
                     'row_size' => 65536,
                     'delimiter' => ',',
                     'enclosure' => '"',
                     'basedir' => null,
                     'internal_locale' => 'ja_JP.UTF-8',
                     );

  public function __construct($options = array(), $messages = array())
  {
    parent::__construct($options, $messages);

    if (setlocale(LC_ALL, $this->getOption('internal_locale')) === false) {
      throw new CsvReaderException('setting locale failure: '.$this->getOption('internal_locale'));
    }
    stream_filter_register("convert.mbstring.*",
                           "Stream_Filter_Mbstring");
  }

  public function fopen()
  {
    $file = $this->getOption('file');
    if ($this->hasOption('basedir') && !preg_match('|^/|', $file)) {
      // fileが/以外から始まっている場合、先頭にbasedirを追加する
      $file = $this->getOption('basedir')."/".$file;
    }
    if (!file_exists($file)) {
      throw new CsvReaderException('input file not exists: '.$file);
    }
    $fp = fopen($file, 'r');
    if ($fp === false) {
      throw new CsvReaderException('failed to open input file.');
    }
    $filter_name = sprintf('convert.mbstring.encoding.%s:UTF-8',
                           $this->getOption('charset'));

    $s_filter = stream_filter_append($fp, $filter_name, STREAM_FILTER_READ);
    if ($s_filter === false) {
      fclose($fp);
      throw new CsvReaderException('Cannot append stream filter: '. $filter_name);
    }
    $this->is_eof = false;
    return $fp;
  }

  public function fclose()
  {
    if (is_resource($this->fp)) {
      fclose($this->fp);
      $this->fp = null;
    }
  }

  /**
   * This method resets the file pointer.
   *
   * @access public
   */
  public function rewind()
  {
    $this->lineNumber = 0;
    if (is_resource($this->fp)) {
      rewind($this->fp);
    } else {
      $this->fp = $this->fopen();
    }
    return $this->fetchLine();
  }

  /**
   * This method returns the current csv row as a 2 dimensional array
   *
   * @access public
   * @return array The current csv row as a 2 dimensional array
   */
  public function current() {
    return $this->currentValues;
  }

  /**
   * This method returns the current line number.
   *
   * @access public
   * @return int The current line number
   */
  public function key() {
    return sprintf("%s:%d", $this->getOption('file'),$this->lineNumber);
  }

  /**
   * This method checks if the end of file is reached.
   *
   * @access public
   * @return boolean Returns true on EOF reached, false otherwise.
   */
  public function next() {
    return $this->fetchLine();
  }

  /**
   * This method checks if the next row is a valid row.
   *
   * @access public
   * @return boolean If the next row is a valid row.
   */
  public function valid() {
    if ($this->is_eof) {
      return false;
    }
    return true;
  }

  protected function fetchLine() {
    if ($this->is_eof) {
      return false;
    } elseif(feof($this->fp)) {
      fclose($this->fp);
      $this->is_eof = true;
      $this->fp = null;
      return false;
    }
    if (is_resource($this->fp)) {
      $this->lineNumber++;
      $this->currentValues = fgetcsv($this->fp,
                                     $this->getOption('row_size'),
                                     $this->getOption('delimiter'),
                                     $this->getOption('enclosure'));
    }
    return true;
  }
}
