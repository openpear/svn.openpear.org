<?php
abstract class Text_CsvReader_Filter extends Text_CsvReader_Base implements OuterIterator
{
  protected
    $iterator = null,
    $targetOptions = array();

  /**
   * Constructor.
   *
   * @param Iterator $iterator   An iterator
   * @param array $options       An array of options
   * @param array $messages      An array of error messages
   */
  public function __construct(Iterator $iterator, $options = array(), $messages = array())
  {
    $this->iterator = $iterator;
    parent::__construct($options, $messages);
  }

  public function getInnerIterator()
  {
    return $this->iterator;
  }

  public function valid()
  {
    return $this->iterator->valid();
  }

  public function key()
  {
    return $this->iterator->key();
  }

  public function current()
  {
    return $this->iterator->current();
  }

  public function next()
  {
    return $this->iterator->next();
  }

  public function rewind()
  {
    $this->initialize();
    return $this->iterator->rewind();
  }

  protected function initialize()
  {
  }
}
