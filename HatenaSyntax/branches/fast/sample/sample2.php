<?php
include_once dirname(__FILE__) . '/../code/HatenaSyntax.php';

$str = '*hogehoge

fugafuga';

var_dump(HatenaSyntax::parse($str));
/*
object(HatenaSyntax_Node)#216 (2) {
  ["type:protected"]=>
  string(4) "root"
  ["data:protected"]=>
  array(3) {
    [0]=>
    object(HatenaSyntax_Node)#213 (2) {
      ["type:protected"]=>
      string(6) "header"
      ["data:protected"]=>
      array(2) {
        ["level"]=>
        int(0)
        ["body"]=>
        array(1) {
          [0]=>
          string(8) "hogehoge"
        }
      }
    }
    [1]=>
    object(HatenaSyntax_Node)#214 (2) {
      ["type:protected"]=>
      string(14) "emptyparagraph"
      ["data:protected"]=>
      int(1)
    }
    [2]=>
    object(HatenaSyntax_Node)#215 (2) {
      ["type:protected"]=>
      string(9) "paragraph"
      ["data:protected"]=>
      array(1) {
        [0]=>
        string(8) "fugafuga"
      }
    }
  }
}

*/
