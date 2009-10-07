<?php
class Text_CsvReader_Validator_NColumn extends Text_CsvReader_Validator
{
  protected $requiredOptions = array('ncolumn');

  public function validateAll($values) {
    $ncolumn = sizeof($values);
    if ($ncolumn !== $this->getOption('ncolumn')) {
      $error = sprintf('カラム数が%dではありません: %d',
                       $this->getOption('ncolumn'), $ncolumn);
      return $error;
    }
    return null;
  }
}
