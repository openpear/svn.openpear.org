<?php
require 'Text/PictgramConverter';

$utf = PictgramConverter::convert($s, PictgramConverter::DOCOMO);
//絵文字を含むsjis(cp932)文字列をutf-8に変換


echo PictgramConverter::restore($s, PictgramConverter::EZWEB);
//ezwebの絵文字を含むsjis文字列に変換




?>
