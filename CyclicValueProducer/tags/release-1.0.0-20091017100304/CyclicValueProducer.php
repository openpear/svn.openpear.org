<?php

  /**
   * CyclicValueProducer - 循環的に値を出力するイテレータ
   *
   * @package  CyclicValueProducer
   * @author  Yoshio HANAWA <y@hnw.jp>
   * @copyright  2009 Yoshio HANAWA
   * @license  http://creativecommons.org/licenses/BSD/    New BSD Licence
   * @link  http://openpear.org/package/CyclicValueProducer
   */

class CyclicValueProducer implements IteratorAggregate
{
  protected $cyclic_values;

  public function __construct($cyclic_values, $limit = 0)
  {
    if (!is_array($cyclic_values)) {
      throw new InvalidArgumentException(sprintf('%s expects parameter 1 to be %s, %s given',
                                                 __METHOD__,
                                                 'array',
                                                 gettype($cyclic_values)
                                                 ));
    }
    $this->cyclic_values = $cyclic_values;
    $this->limit = $limit;
  }

  public function getIterator()
  {

    $it = new InfiniteIterator(new ArrayIterator($this->cyclic_values));
    if ($this->limit > 0) {
      $it = new LimitIterator($it, 0, $this->limit);
    }
    return $it;
  }
}
