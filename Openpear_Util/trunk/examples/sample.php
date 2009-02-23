<?php

require_once 'Openpear/Util.php';

Openpear_Util::import('array_val');

$ar = array(
    'hoge' => 1,
    );
var_dump(array_val($ar, 'hoge'));
var_dump(array_val($ar, 'fuga'));

?>