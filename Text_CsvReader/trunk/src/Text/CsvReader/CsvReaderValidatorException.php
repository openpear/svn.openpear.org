<?php
class CsvReaderValidatorException extends CsvReaderException {
  protected $validatorErrors = array();
  public function __construct($validatorErrors)
  {
    $num_error = sizeof($validatorErrors);
    $msg = sprintf('%d errors occurred. stopped processing.', $num_error);
    parent::__construct($msg);
    $this->setErrors($validatorErrors);
  }
  public function setErrors($errors) {
    $this->validatorErrors = $errors;
  }
  public function getErrors() {
    return $this->validatorErrors;
  }
}

