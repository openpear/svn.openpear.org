<?php

  /**
   * LazyFileIterator - 必要になってからファイルを開く、SplFileObjectのラッパー
   *
   * @package  LazyFileIterator
   * @author  Yoshio HANAWA <y@hnw.jp>
   * @copyright  2009 Yoshio HANAWA
   * @license  http://creativecommons.org/licenses/BSD/    New BSD Licence
   * @link  http://openpear.org/package/LazyFileIterator
   */

class LazyFileIterator implements OuterIterator
{
  protected $it;
  protected $is_eof = false;
  protected $spl_file_object_params = array();

  public function __construct($file_name, $open_mode = 'r',
                              $use_include_path = false, $context = NULL)
  {
    $this->spl_file_object_params['file_name'] = $file_name;
    $this->spl_file_object_params['open_mode'] = $open_mode;
    $this->spl_file_object_params['use_include_path'] = $use_include_path;
    $this->spl_file_object_params['context'] = $context;
  }
  public function getInnerIterator()
  {
    if ($this->it === null) {
      $this->is_eof = false;
      if ($this->spl_file_object_params['context'] === null) {
        $this->it =
          new SplFileObject($this->spl_file_object_params['file_name'],
                            $this->spl_file_object_params['open_mode'],
                            $this->spl_file_object_params['use_include_path']);
      } else {
        $this->it =
          new SplFileObject($this->spl_file_object_params['file_name'],
                            $this->spl_file_object_params['open_mode'],
                            $this->spl_file_object_params['use_include_path'],
                            $this->spl_file_object_params['context']);
      }
      if (!file_exists($this->spl_file_object_params['file_name'])) {
        $this->it = new NoRewindIterator($this->it);
      }
    }
    return $this->it;
  }
  public function current() {
    return $this->_call_spl_file_obj("current");
  }
  public function key()     {
    return $this->_call_spl_file_obj("key");
  }
  public function next()
  {
    return $this->_call_spl_file_obj("next");
  }
  public function rewind()  {
    $this->is_eof = false;
    return $this->_call_spl_file_obj("rewind");
  }
  public function valid()   {
    $ret = $this->_call_spl_file_obj("valid");
    if ($ret === false) {
      $this->it = null;
      $this->is_eof = true;
    }
    return $ret;
  }
  protected function _call_spl_file_obj($method)
  {
    if ($this->is_eof) {
      $ret = ($method === "valid") ? false :  null;
    } else {
      $it = $this->getInnerIterator();
      $ret = call_user_func(array($it, $method));
    }
    return $ret;
  }
}
