<?php
class CsvReaderValidatorException extends CsvReaderException {
  protected $validatorError = array();
  public function __construct($msg, $errors = array())
  {
    parent::__construct($msg);
    if (!is_array($errors)) {
      $column_index = $errors;
      // todo
      $this->setErrors(array($msg));
    } else {
      if ($errors !== array()) {
        $this->setErrors($errors);
      } else {
        $this->setErrors(array($msg));
      }
    }
  }
  public function addErrors($errors) {
    if ($errors instanceof CsvReaderValidatorException) {
      $errors = $errors->getErrors();
    }
    $this->validatorErrors = array_merge($this->validatorErrors, $errors);
  }
  public function setErrors($errors) {
    $this->validatorErrors = $errors;
  }
  public function getErrors() {
    return $this->validatorErrors;
  }
}

