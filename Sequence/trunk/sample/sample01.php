<?php
include_once dirname(__FILE__) . '/../src/seq.php';

seq(1,2,3,4)->pick(0, -1)->tovar($a, $b)->dump();

seq(1,2,3)->slice(2, 3)->dump();

seq("abc", false, "defg", "hgoe")->harvest()->map('strlen')->dump();

list($a, $b) = seq(1, 2);