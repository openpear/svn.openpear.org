<?php

  /**
   * LoggerIterator - 既存のイテレータにログ取得機能を追加するイテレータ
   *
   * @package  LoggerIterator
   * @author  Yoshio HANAWA <y@hnw.jp>
   * @copyright  2009 Yoshio HANAWA
   * @license  http://creativecommons.org/licenses/BSD/    New BSD Licence
   * @link  http://openpear.org/package/LoggerIterator
   */

class LoggerIterator Implements OuterIterator
{
  protected $it;
  protected $mode = 0x0;
  const QUIET = 0x0;
  const VERBOSE = 0x01;

  public function __construct($it, $mode = 0x0)
  {
    $this->it = $it;
    $this->mode = $mode;
  }
  public function getInnerIterator()
  {
    return $this->it;
  }
  public function current() { return $this->__call("current"); }
  public function key()     { return $this->__call("key"); }
  public function next()    { return $this->__call("next"); }
  public function rewind()  { return $this->__call("rewind"); }
  public function valid()   { return $this->__call("valid"); }

  public function __call($method, $params = array())
  {
    $msg = "";
    $caught_exception = null;
    try {
      $ret = call_user_func_array(array($this->it, $method), $params);
    } catch (Exception $e) {
      $ret = ($method === "valid") ? false :  null;
      $msg = "Caught Exception: ".$e->getMessage(). "\n";
      $caught_exception = $e;
    }
    if ($this->mode && self::VERBOSE) {
      $msg .= sprintf("%s: %s::%s(%s) = %s\n",
                      __CLASS__,
                      get_class($this->it), $method,
                      join(",", $params),
                      print_r($ret, true));
    }
    if ($msg !== "") {
      fputs(STDERR, $msg);
    }
    if ($caught_exception instanceof Exception) {
      throw $caught_exception;
    }
    return $ret;
  }
}
