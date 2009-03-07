<?php
//require_once('Image/0.1.0-alpha/Image_Colorful.php');
require_once('./Image_Colorful.php');

$image = new Image_Colorful(700,200,175,1);
$image->addTexts('ImageColorful', './font/altan.ttf', '50', '35', '70');
$image->addTexts('camelmasa', './font/altan.ttf', '35', '400', '170');
$image->addColors(150,150,150);
$image->getGenerateImage('png');
$image->saveGenerateImage('a.png','png');
?>
