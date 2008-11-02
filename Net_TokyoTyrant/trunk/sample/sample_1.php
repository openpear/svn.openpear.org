<?php
require_once '../Net/TokyoTyrant.php';
$tt = new Net_TokyoTyrant();
$tt->connect('localhost', 1978);
$tt->put('oyomesan', 'nounai');
var_dump($tt->get('oyomesan'));
$tt->put('kanozyo', 'pc no naka');
$tt->put('kareshi', 'otoko ha chotto');
var_dump($tt->mget(array('oyomesan', 'kanozyo')));
var_dump($tt->fwmkeys('ka', 100)); //ka

