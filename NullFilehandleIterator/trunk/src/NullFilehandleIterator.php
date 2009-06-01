<?php
require_once('LazyFileIterator.php');
require_once('LoggerIterator.php');
require_once('IgnoreExceptionIterator.php');

  /**
   * Nullfilehandleiterator - 
   *
   * @package  NullFilehandleIterator
   * @author  Yoshio HANAWA <y@hnw.jp>
   * @copyright  2009 Yoshio HANAWA
   * @license  http://creativecommons.org/licenses/BSD/    New BSD Licence
   * @link  http://openpear.org/package/NullFilehandleIterator
   */

class NullFilehandleIterator implements IteratorAggregate
{
  protected $files;

  public function __construct()
  {
    $files = $_SERVER['argv'];
    array_shift($files); // remove filename of myself
    if ($files === array()) {
      $files[] = 'php://stdin';
    }
    $this->files = $files;
  }
  public function getIterator() {
    $it = new AppendIterator();
    foreach($this->files as $filename) {
      $it->append(new IgnoreExceptionIterator(new LoggerIterator(new LazyFileIterator($filename, 'r'), LoggerIterator::QUIET)));
      //$it = new LazyFileIterator($filename, 'r');
    }
    $this->files = array();
    return $it;
  }
}
