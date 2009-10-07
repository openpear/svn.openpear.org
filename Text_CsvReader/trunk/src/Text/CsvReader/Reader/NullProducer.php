<?php
class Text_CsvReader_Reader_NullProducer extends Text_CsvReader_Reader
{
  protected
    $lineNumber = 0,
    $requiredOptions = array('count');

  /**
   * This method resets the file pointer.
   *
   * @access public
   */
  public function rewind()
  {
    $this->lineNumber = 1;
  }

  /**
   * This method returns the current csv row as a 2 dimensional array
   *
   * @access public
   * @return array The current csv row as a 2 dimensional array
   */
  public function current()
  {
    return array();
  }

  /**
   * This method returns the current line number.
   *
   * @access public
   * @return int The current line number
   */
  public function key()
  {
    return sprintf("%s:%d", "NullProducer", $this->lineNumber);
  }

  /**
   * This method checks if the end of file is reached.
   *
   * @access public
   * @return boolean Returns true on EOF reached, false otherwise.
   */
  public function next()
  {
    $this->lineNumber++;
  }

  /**
   * This method checks if the next row is a valid row.
   *
   * @access public
   * @return boolean If the next row is a valid row.
   */
  public function valid()
  {
    if ($this->lineNumber > $this->getOption('count')) {
      return false;
    }
    return true;
  }
}
