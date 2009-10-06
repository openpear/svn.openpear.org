<?php
class Text_CsvReader_ValidatorManager extends Text_CsvReader_Filter
{
  protected
    $options = array(
                     'validators' => array(),
                     'max_error' => 10,
                     ),
    $errors = array();

  protected function logException(Exception $e)
  {
    $errors = $e->getErrors();
    foreach ($errors as $error) {
      $error = sprintf("%s:%s", parent::key(), $error);
      array_push($this->errors, $error);
      if (sizeof($this->errors) >= $this->getOption('max_error')) {
        $error = sprintf('%d errors occurred', sizeof($this->errors));
        array_push($this->errors, $error);
        $this->throwAllError();
      }
    }
  }
  protected function throwAllError()
  {
    throw new CsvReaderValidatorException('', $this->errors);
  }
  public function rewind()
  {
    $ret = parent::rewind();
    $this->errors = array();
    return $ret;
  }
  public function valid()
  {
    $value = parent::valid();
    if ($value === false && sizeof($this->errors) > 0) {
      // イテレータが最終行に到達した時点で、貯めておいた例外を投げる。
      $this->throwAllError();
      return false; // dummy: never executed
    }
    return $value;
  }
  public function current()
  {
    $values = parent::current();
    $validators = $this->getOption('validators');
    foreach ($validators as $validator) {
      try {
        $validator->validateAll($values);
      } catch (CsvReaderValidatorException $e) {
        $this->logException($e);
      }
    }
    return $values;
  }
}
