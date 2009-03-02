<?php
require_once('Image_Colorful.php');

$image = new Image_Colorful(700,200,175,1);
$image->setTitle('ImageColorful', 'altan.ttf', '50', '35', '70');
$image->setAuthor('camelmasa', 'altan.ttf', '35', '400', '170');
$image->getGenerateImage('png');
?>
