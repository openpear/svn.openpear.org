<?php
include_once dirname(__FILE__) . '/../code/PEG.php';

/*
 * 単語にヒットするパーサ。
 * 
 * EBNF:
 * word := (PEG::alphabet | "_") (PEG::alphabet | PEG::digit | "_")+
 */

$word = PEG::join(PEG::seq(
    PEG::choice(PEG::alphabet(), PEG::token('_')), 
    PEG::many(PEG::choice(PEG::alphabet(), PEG::digit(), PEG::token('_')))
));

var_dump(PEG::parse($word, 'a')); //=> 'a'
var_dump(PEG::parse($word, 'hogehoge')); //=> 'hogehoge'
var_dump(PEG::parse($word, 'some_id')); //=> 'some_id'
var_dump(PEG::parse($word, '  ')); //=> パースに失敗する
var_dump(PEG::parse($word, 'hoge fuga')); //=> パースはコンテキストの途中で止まり 'hoge'が返る