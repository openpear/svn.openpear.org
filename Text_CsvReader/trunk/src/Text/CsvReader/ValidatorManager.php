<?php
class Text_CsvReader_ValidatorManager extends Text_CsvReader_Filter
{
  protected
    $options = array(
                     'validators' => array(),
                     'max_error' => 10,
                     ),
    $validatorErrors = array();

  public function rewind()
  {
    $ret = parent::rewind();
    $this->validatorErrors = array(); // 過去に発生したエラーをリセット
    return $ret;
  }
  public function valid()
  {
    $value = parent::valid();
    if ($value === false && $this->numErrors() > 0) {
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
    $caughtErrors = array();
    foreach ($validators as $validator) {
      $errors = $validator->validateAll($values);
      if ($errors) {
        if (is_array($errors)) {
          $caughtErrors = CsvReaderUtil::arrayDeepMerge($caughtErrors, $errors);
        } else {
          // カラム名が不明、おそらく行全体のエラー
          $caughtErrors[''][] = $errors;
        }
      }
    }
    if ($caughtErrors) {
      $this->logErrors($caughtErrors);
    }
    return $values;
  }

  protected function numErrors()
  {
    return sizeof($this->validatorErrors);
  }
  protected function logErrors($caughtErrors)
  {
    $key = parent::key();
    $value = parent::current();
    foreach ($caughtErrors as $column_index => $errors) {
      foreach ($errors as $error) {
        if (isset($value[$column_index])) {
          $error = sprintf("%d:%s: '%s'",
                           $column_index, $error, $value[$column_index]);
        }
        $error = sprintf("%s:%s", $key, $error);
        $this->logError($error);
      }
    }
  }
  protected function logError($error)
  {
    $this->validatorErrors[] = $error;
    if ($this->numErrors() >= $this->getOption('max_error')) {
      $this->throwAllError();
    }
  }
  protected function throwAllError()
  {
    throw new CsvReaderValidatorException($this->validatorErrors);
  }
}
