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

$p = new PEG; // ファクトリークラス

$item = $p->choice();
$paren = $p->sequence();
$proc = create_function('$v', 'return $v[1];');

$item->with($paren)
     ->with($p->anything());

$paren_item_mat = $p->sequence();
$paren_item_mat->with($p->lookaheadNot($p->token(')')))
               ->with($item);
$paren_item = $p->callbackAction($proc, $paren_item_mat);
     
$paren->with($p->token('('))
      ->with($p->many($paren_item))
      ->with($p->token(')'));

$parser = $p->many($item);

$str = 'abc(def(ghi)(jkl(mno)))';
var_dump($parser->parse($p->context($str)));
/* 結果
array(4) {
  [0]=>
  string(1) "a"
  [1]=>
  string(1) "b"
  [2]=>
  string(1) "c"
  [3]=>
  array(3) {
    [0]=>
    string(1) "("
    [1]=>
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
        string(1) "("
        [1]=>
        array(3) {
          [0]=>
          string(1) "g"
          [1]=>
          string(1) "h"
          [2]=>
          string(1) "i"
        }
        [2]=>
        string(1) ")"
      }
      [4]=>
      array(3) {
        [0]=>
        string(1) "("
        [1]=>
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
            string(1) "("
            [1]=>
            array(3) {
              [0]=>
              string(1) "m"
              [1]=>
              string(1) "n"
              [2]=>
              string(1) "o"
            }
            [2]=>
            string(1) ")"
          }
        }
        [2]=>
        string(1) ")"
      }
    }
    [2]=>
    string(1) ")"
  }
}
 */