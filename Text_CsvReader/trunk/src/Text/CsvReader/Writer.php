<?php
abstract class Text_CsvReader_Writer extends Text_CsvReader_Base
{
  abstract public function initialize();
  abstract public function write($values);
  abstract public function finalize();
  abstract public function rollback();
}