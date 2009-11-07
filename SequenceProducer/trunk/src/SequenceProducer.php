<?php

  /**
   * SequenceProducer - 連続値を出力するイテレータ
   *
   * @package  SequenceProducer
   * @author  Yoshio HANAWA <y@hnw.jp>
   * @copyright  2009 Yoshio HANAWA
   * @license  http://creativecommons.org/licenses/BSD/    New BSD Licence
   * @link  http://openpear.org/package/SequenceProducer
   */

class SequenceProducer implements Iterator
{
  protected
    $min = null,
    $max = null;

  public function __construct($min, $max=null)
  {
    if (is_array($min) || is_object($min)) {
      throw new InvalidArgumentException(sprintf('%s expects parameter 1 to be %s, %s given',
                                                 __METHOD__,
                                                 'integer',
                                                 gettype($values)
                                                 ));

    }
    if (is_array($max) || is_object($max)) {
      throw new InvalidArgumentException(sprintf('%s expects parameter 2 to be %s, %s given',
                                                 __METHOD__,
                                                 'integer',
                                                 gettype($max)
                                                 ));

    }
    if (isset($max) && ($min > $max)) {
      throw new InvalidArgumentException(sprintf('%s :parameter 1 must be smaller than  parameter 2',
                                                 __METHOD__
                                                 ));

    }
    $this->min = $min;
    $this->max = $max;
  }
  public function valid()
  {
    if (isset($this->max)) {
      return ($this->currentValue <= $this->max);
    }
    return true;
  }
  public function key()
  {
    return $this->rowNumber;
  }
  public function current()
  {
    return $this->currentValue;
  }
  public function rewind()
  {
    $this->rowNumber = 0;
    $this->currentValue = $this->min;
  }
  public function next()
  {
    $this->rowNumber++;
    $this->currentValue++;
  }

}