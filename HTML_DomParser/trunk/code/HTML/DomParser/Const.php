<?php
/**
* Name: HTML_DomParser_Const
* Modifier: Shuhei Tanuma <shuhei.tanuma at gmail.com>
* Version: 1.10.1
* Original: http://sourceforge.net/projects/simplehtmldom/
* 
* このライブラリはS.C. Chen氏作成のsimple_html_domをPEARライクな
* 構成にするために改変したものです。
* このライブラリのバグについてはS.C. Chen氏ではなく
* Modifierの Shuhei Tanuma <shuhei.tanuma at gmail.com>まで
* お願いいたします。
* 
*
* Licensed under The MIT License
* Redistributions of files must retain the above copyright notice.
**/



/*******************************************************************************
OriginalName: simple_html_dom
Website: http://sourceforge.net/projects/simplehtmldom/
Author: S.C. Chen <me578022@gmail.com>
Acknowledge: Jose Solorzano (https://sourceforge.net/projects/php-html/)
Contributions by:
    Yousuke Kumakura (Attribute filters)
    Vadim Voituk (Negative indexes supports of "find" method)
    Antcs (Constructor with automatically load contents either text or file/url)
Licensed under The MIT License
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

class HTML_DomParser_Const{
  const TYPE_ELEMENT  = 1;
  const TYPE_COMMNET  = 2;
  const TYPE_TEXT     = 3;
  const TYPE_ENDTAG   = 4;
  const TYPE_ROOT     = 5;
  const TYPE_UNKNOWN  = 6;
  const QUOTE_DOUBLE  = 0;
  const QUOTE_SINGLE  = 1;
  const QUOTE_NO      = 3;
  const INFO_BEGIN    = 0;
  const INFO_END      = 1;
  const INFO_QUOTE    = 2;
  const INFO_SPACE    = 3;
  const INFO_TEXT     = 4;
  const INFO_INNER    = 5;
  const INFO_OUTER    = 6;
  const INFO_ENDSPACE = 7;
  
  private function __construct(){}
}