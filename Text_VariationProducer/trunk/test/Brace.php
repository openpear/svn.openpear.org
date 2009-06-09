<?php
include_once(dirname(__FILE__) . '/t/t.php');

$lime = new lime_test(15, new lime_output_color());
$strings1 = new Text_VariationProducer('{a}');
$strings2 = new Text_VariationProducer('{ab}');
$strings3 = new Text_VariationProducer('{ab,cd}');
$strings4 = new Text_VariationProducer('{ab[01],cd[23]}');
$strings5 = new Text_VariationProducer('{ab\x20,cd\0,ef\n}');
$strings6 = new Text_VariationProducer('{,0}');
$strings7 = new Text_VariationProducer('A{,0}');
$strings8 = new Text_VariationProducer('{,0}B');
$strings9 = new Text_VariationProducer('A{,0}B');
$strings10 = new Text_VariationProducer('A{0,,}B');
$strings11 = new Text_VariationProducer('{\,\\,\\\,\\\\,}');
$strings12 = new Text_VariationProducer('{\x5c,\x2c,\x7d}');
$strings13 = new Text_VariationProducer('{,[kstnhmyrwgzdbp]}[aiueo]');
$strings14 = new Text_VariationProducer('{,[kstnhmyrwgzdbp]}[aiueo]{,[kstnhmyrwgzdbp]}[aiueo]');
$strings15 = new Text_VariationProducer('{a,a,a,[a],\x61}{,,}');

//--
$lime->diag('braces');
$lime->ok(iterator_to_array($strings1) === array('a'));
$lime->ok(iterator_to_array($strings2) === array('ab'));
$lime->ok(iterator_to_array($strings3) === array('ab', 'cd'));
$lime->ok(iterator_to_array($strings4) === array('ab0', 'ab1', 'cd2', 'cd3'));
$lime->ok(iterator_to_array($strings5) === array('ab ', "cd\0", "ef\n"));
$lime->ok(iterator_to_array($strings6) === array('', '0'));
$lime->ok(iterator_to_array($strings7) === array('A', 'A0'));
$lime->ok(iterator_to_array($strings8) === array('B', '0B'));
$lime->ok(iterator_to_array($strings9) === array('AB', 'A0B'));
$lime->ok(iterator_to_array($strings10) ===  array('A0B', 'AB', 'AB'));
$lime->ok(iterator_to_array($strings11) ===  array(',,\\', '\\', ''));
$lime->ok(iterator_to_array($strings12) ===  array('\\', ',', '}'));
$lime->ok(iterator_count($strings13) ===  75);
$lime->ok(iterator_count($strings14) ===  5625);
$lime->ok(iterator_to_array($strings15) ===  array_fill(0, 15, 'a'));