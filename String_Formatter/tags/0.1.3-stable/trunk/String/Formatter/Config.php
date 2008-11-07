<?php
/**
 * String_Formatter_Config
 * String Formatter Config Class
 *
 * @version: 0.1.0 <2008/10/26 16:49>
 * @package: String_Formatter
 * @author: Shuhei Tanuma <shuhei.tanuma at gmail.com>
 *
**/
final class String_Formatter_Config extends String_Formatter_Core{
  protected $ldelimiter;
  protected $rdelimiter;
  protected $delimiter;
  protected $regexdelimiter;

  protected function __construct(){
    $this->delimiter =  String_Formatter_Const::DefaultDelimiter;
    $this->ldelimiter = String_Formatter_Const::DefaultLeftDelimiter;
    $this->rdelimiter = String_Formatter_Const::DefaultRightDelimiter;
    $this->regexdelimiter = String_Formatter_Const::DefaultRegexDelimiter;
    $this->regexp = String_Formatter_Const::DefaultRegexp;
  }
}