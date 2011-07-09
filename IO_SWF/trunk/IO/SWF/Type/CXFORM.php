<?php

/*
 * 2011/7/9- (c) yoya@awm.jp
 */

require_once 'IO/Bit.php';
require_once dirname(__FILE__).'/../Type.php';

class IO_SWF_Type_CXFORM extends IO_SWF_Type {
    static function parse(&$reader, $opts = array()) {
    	$cxform = array();
    	$hasAddTerms = $reader->getUIBit();
    	$hasMultiTerms = $reader->getUIBit();
    	$cxform['HasAddTerms'] = $hasAddTerms;
    	$cxform['HasMultiTerms'] = $hasMultiTerms;
	$nbits = $reader->getUIBits(4);
	if ($hasMultiTerms) {
	    $cxform['RedMultiTerm']   = $reader->getSIBits($nbits);
	    $cxform['GreenMultiTerm'] = $reader->getSIBits($nbits);
	    $cxform['BlueMultiTerm']  = $reader->getSIBits($nbits);
	}
	if ($hasAddTerms) {
	    $cxform['RedAddTerm']   = $reader->getSIBits($nbits);
	    $cxform['GreenAddTerm'] = $reader->getSIBits($nbits);
	    $cxform['BlueAddTerm']  = $reader->getSIBits($nbits);
	}
    	return $cxform;
    }
    static function build(&$writer, $cxform, $opts = array()) {
        $nbits = 0;
	$hasAddTerms = 0;
	$hasMultiTerms = 0;
	$multi_term_list = array('RedMultiTerm', 'GreenMultiTerm', 'BlueMultiTerm');
	foreach ($multi_term_list as $term) {
	    if (isset($cxform[$term])) {
	        $hasMultiTerms = 1;
		$need_bits = $writer->need_bits_signed($cxform[$term]);
		if ($nbits < $need_bits){
		    $nbits = $need_bits;
		}
	    }
	}
	$add_term_list = array('RedAddTerm', 'GreenAddTerm', 'BlueAddTerm');
	foreach ($add_term_list as $term) {
	    if (isset($cxform[$term])) {
	        $hasAddTerms = 1;
		$need_bits = $writer->need_bits_signed($cxform[$term]);
		if ($nbits < $need_bits){
		    $nbits = $need_bits;
		}
	    }
	}
	$writer->putUIBit($hasAddTerms);
	$writer->putUIBit($hasMultiTerms);
	$writer->putUIBits($nbits, 4);
	if ($hasMultiTerms) {
	    $writer->putSIBits($cxform['RedMultiTerm'],   $nbits);
	    $writer->putSIBits($cxform['GreenMultiTerm'], $nbits);
	    $writer->putSIBits($cxform['BlueMultiTerm'],  $nbits);
	}
	if ($hasAddTerms) {
	  $writer->putSIBits($cxform['RedAddTerm'],   $nbits);
	  $writer->putSIBits($cxform['GreenAddTerm'], $nbits);
	  $writer->putSIBits($cxform['BlueAddTerm'],  $nbits);
	}
    }
    static function string($cxform, $opts = array()) {
      if (($cxform['HasMultiTerms'] == 0) && ($cxform['HasAddTerms'] == 0)) {
	  return '(No Data: CXFORM)';
      }
      $text = '';
      if ($cxform['HasMultiTerms']) {
	  $text .= sprintf("MultiTerms:(%02x,%02x,%02x)", $cxform['RedMultiTerm'], $cxform['GreenMultiTerm'], $cxform['BlueMultiTerm']);
      }
      if ($cxform['HasAddTerms']) {
	  if ($cxform['HasMultiTerms']) {
	      $text .= ' ';
	  }
	  $text .= sprintf("AddTerms:(%02x,%02x,%02x)", $cxform['RedAddTerm'], $cxform['GreenAddTerm'], $cxform['BlueAddTerm']);
      }
      return $text;
    }
}
