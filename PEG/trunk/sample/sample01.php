<?php
include_once dirname(__FILE__) . '/../code/PEG.php';

/**
 * 括弧の対応をとる再帰的なパーサのサンプル
 * 認識した括弧を用いて文字列を階層化する
 * 
 * パーサのEBNFはこんな感じ
 * item := paren | anything
 * paren_item := (?! ")") item
 * paren := "(" paren_item* ")"
 * parser := item*
 */


$item = PEG::choice();
$paren = PEG::ref();

$item->with($paren)
     ->with(PEG::anything());

$paren_item_mat = PEG::sequence();
$paren_item_mat->with(PEG::lookaheadNot(PEG::token(')')))
               ->with($item);
$paren_item = PEG::second($paren_item_mat);
     
$paren->set(PEG::pack(PEG::token('('), PEG::many($paren_item), PEG::token(')')));

$parser = PEG::many($item);

$str = 'abc(def(ghi)(jkl(mno)))';
var_dump($parser->parse(PEG::context($str)));
/* 結果
array(4) {
  [0]=>
  string(1) "a"
  [1]=>
  string(1) "b"
  [2]=>
  string(1) "c"
  [3]=>
  array(5) {
    [0]=>
    string(1) "d"
    [1]=>
    string(1) "e"
    [2]=>
    string(1) "f"
    [3]=>
    array(3) {
      [0]=>
      string(1) "g"
      [1]=>
      string(1) "h"
      [2]=>
      string(1) "i"
    }
    [4]=>
    array(4) {
      [0]=>
      string(1) "j"
      [1]=>
      string(1) "k"
      [2]=>
      string(1) "l"
      [3]=>
      array(3) {
        [0]=>
        string(1) "m"
        [1]=>
        string(1) "n"
        [2]=>
        string(1) "o"
      }
    }
  }
}

 */