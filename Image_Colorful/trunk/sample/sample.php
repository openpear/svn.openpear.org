<?php
require_once('./Image_Colorful.php');

$image = new Image_Colorful(685,100,175,1);
$image->addColors(150,150,150);
$image->addTexts('upperleft', './font/altan.ttf', '30', '10', '10', array(255,255,255), 'UPPER_LEFT');
$image->addTexts('upperright', './font/altan.ttf', '30', '10', '10', array(255,255,255), 'UPPER_RIGHT');
$image->addTexts('center', './font/altan.ttf', '30', '0', '0', array(255,255,255), 'CENTER');
$image->addTexts('leftlower', './font/altan.ttf', '30', '10', '10', array(255,255,255), 'LEFT_LOWER');
$image->addTexts('rightlower', './font/altan.ttf', '30', '10', '10', array(255,255,255), 'RIGHT_LOWER');
$image->getGenerateImage('png');
?>
