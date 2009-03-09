<?php
require_once('Image/Image_Colorful_Word.php');

$image = new Image_Colorful_Word('ImageCalorfulWord', './font/altan.ttf', 50, array(5,5,5,5), array(255,255,255), 50, 1);
$image->addColors(150,150,150);
$image->getGenerateImage('png');
?>
