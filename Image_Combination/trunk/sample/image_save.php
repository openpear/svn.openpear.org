<?php

require_once ('Image/Combination.php');

$image = new Image_Combination(100,100);
$image->setImageType('gif');
$image->push(array('file' => 'body.gif',   'x' => 30, 'y' => 5));
$image->push(array('file' => 'weapon.gif', 'x' => 5,  'y' => 35));
$image->push(array('file' => 'shield.gif', 'x' => 55, 'y' => 45));
$image->save('person.gif');
