<?php
class Text_CsvReader_Validator_Regex extends Text_CsvReader_Validator
{
  protected $requiredOptions = array('pattern');
  protected $targetOptions = array('pattern');

  protected function validate($value, $column_index)
  {
    $pattern = $this->getOption('pattern', $column_index);
    if (isset($pattern) && !preg_match($pattern, $value)) {
      return array(sprintf('正規表現%sのマッチに失敗しました',$pattern));
    }
    return null;
  }
}
