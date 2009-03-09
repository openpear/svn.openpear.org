<?php
/**
 * Class for Image_Colorful 
 *
 * LICENSE:New BSD Licence
 *
 * Copyright (c) 2009, Masahiro Saito
 * All rights reserved.
 *
 * @package     Image_Colorful
 * @author      Masahiro Saito <camelmasa@gmail.com>
 * @copyright   2009 Masahiro Saito
 * @license     http://creativecommons.org/licenses/BSD/
 * @version     0.1.3-stable
 * @link        http://camelmasa.com/
 */

require_once('./Colorful.php');

class Image_Colorful extends Colorful
{

	var $_imageheight = 0;

	var $_imagewidth = 0;

	var $_splitheight = 1;

	var $_splitwidth = 1;

	var $_addred = 0;

	var $_addgreen = 0;

	var $_addblue  = 0;

	var $_setred = null;

	var $_setgreen = null;

	var $_setblue  = null;

	var $_text =  array();

	var $_text_size = array();

	var $_text_font = array();

	var $_text_width = array();

	var $_text_height = array();

	var $_text_color = array();

	var $_text_point = array();

	var $_error_message = "";

	var $_flag = true;


	function Image_Colorful($width = '', $height = '', $split_width = 1, $split_height = 1 )
	{
		$this->checkNumber($width);
		$this->checkNumber($height);
		$this->checkNumber($split_width);
		$this->checkNumber($split_height);

		$this->_imageheight = $height; 
		$this->_imagewidth = $width; 
		$this->_splitheight = $split_height; 
		$this->_splitwidth = $split_width; 
	}

	function addTexts($text, $font, $size, $width=0, $height=0, $color=array(255,255,255), $point="UPPER_LEFT"){
		$this->checkError();
		$this->checkCharacter($text);
		$this->checkFont($font);
		$this->checkNumber($size);
		$this->checkNumber($width);
		$this->checkNumber($height);
		$this->checkColor($color[0]);
		$this->checkColor($color[1]);
		$this->checkColor($color[2]);
		$this->_text_point[] = $this->checkPoint($point);

		$this->_text[] = $text;
		$this->_text_font[] = $font;
		$this->_text_size[] = $size;
		$this->_text_width[] = $width;
		$this->_text_height[] = $height;
		$this->_text_color[] = $color;
	}

	function getGenerateImage($image_type = 'gif'){
		$this->checkError();
		$image = $this->GenerateImage();

		switch($image_type){
			case 'gif':
				header('Content-type: image/gif');
				imagegif($image);
				break;
			case 'png':
				header('Content-type: image/png');
				imagepng($image);
				break;
			case 'jpg':
			case 'jpeg':
				header('Content-type: image/jpeg');
				imagejpeg($image);
				break;
			default:
				$this->showError();
				break;
		}

	}

	function saveGenerateImage($image_name = 'image.gif', $image_type = 'gif'){
		$this->checkError();
		$this->checkCharacter($image_name);
		$image = $this->GenerateImage();

		switch($image_type){
			case 'gif':
				imagegif($image, $image_name);
				break;
			case 'png':
				imagepng($image, $image_name);
				break;
			case 'jpg':
			case 'jpeg':
				imagejpeg($image, $image_name);
				break;
			default:
				$this->showError();
				break;
		}

	}

	function GenerateImage(){
		$this->checkError();

		$image = imagecreatetruecolor($this->_imagewidth, $this->_imageheight);
		$image_copy = imagecreatetruecolor($this->_imagewidth, $this->_imageheight);
		imagecolorallocate($image, 0, 0, 0);
		for($i=0;$i<$this->_splitheight;$i++){
			for($j=0;$j<$this->_splitwidth;$j++){
				if($this->_setred !== null){
					$min_red = $this->_setred;
					$max_red = $this->_setred;
				} elseif($this->_addred > 0){
					$min_red = $this->_addred;
					$max_red = 255;
				} else {
					$min_red = 0;
					$max_red = 255 + $this->_addred;
				}

				if($this->_setgreen !== null){
					$min_green = $this->_setgreen;
					$max_green = $this->_setgreen;
				} elseif($this->_addgreen > 0){
					$min_green = $this->_addgreen;
					$max_green = 255;
				} else {
					$min_green = 0;
					$max_green = 255 + $this->_addgreen;
				}

				if($this->_setblue !== null){
					$min_blue = $this->_setblue;
					$max_blue = $this->_setblue;
				} elseif($this->_addblue > 0){
					$min_blue = $this->_addblue;
					$max_blue = 255;
				} else {
					$min_blue = 0;
					$max_blue = 255 + $this->_addblue;
				}

				$color = imagecolorallocate($image, rand($min_red, $max_red), rand($min_green, $max_green), rand($min_blue, $max_blue));
				imagefilledpolygon($image, array(floor((($this->_imagewidth / $this->_splitwidth) * ($j))), floor((($this->_imageheight / $this->_splitheight) * ($i))),
							floor(($this->_imagewidth / $this->_splitwidth) * ($j + 1)), floor((($this->_imageheight / $this->_splitheight) * ($i))),
							floor(($this->_imagewidth / $this->_splitwidth) * ($j + 1)),floor(($this->_imageheight / $this->_splitheight) * ($i + 1)),
							floor(($this->_imagewidth / $this->_splitwidth) * ($j)), floor(($this->_imageheight / $this->_splitheight) * ($i + 1))), 4, $color);
			}
		}

		if(count($this->_text)){
			for($i=0;$i<count($this->_text);$i++){
				$font_color = imagecolorallocate($image, $this->_text_color[$i][0], $this->_text_color[$i][1], $this->_text_color[$i][2]);

				imagecopy($image_copy, $image, 0, $this->_imagewidth, 0, $this->_imageheight, $this->_imagewidth, $this->_imageheight);

				$text_point = imagefttext($image_copy, $this->_text_size[$i], '0', '0', '0', $font_color, $this->_text_font[$i], $this->_text[$i]);
				$base_width = $text_point[6];
				$base_height = $text_point[7];

				switch($this->_text_point[$i]){
					case 'upper_left':
						$text_width = $this->_text_width[$i] - $base_width;
						$text_height = $this->_text_height[$i] - $base_height;
						break;
					case 'upper_right':
						$text_width = $this->_imagewidth + ($text_point[0] - $text_point[2]) - $this->_text_width[$i] - $base_width;
						$text_height = $this->_text_height[$i] - $base_height;
						break;
					case 'left_lower':
						$text_width = $this->_text_width[$i] - $base_width;
						$text_height = $this->_imageheight + ($text_point[7] - $text_point[1]) - $this->_text_height[$i] - $base_height;
						break;
					case 'right_lower':
						$text_width = $this->_imagewidth + ($text_point[0] - $text_point[2]) - $this->_text_width[$i] - $base_width;
						$text_height = $this->_imageheight + ($text_point[7] - $text_point[1]) - $this->_text_height[$i] - $base_height;
						break;
					case 'center':
						$text_width = floor($this->_text_width[$i] - $base_width + (($text_point[0] - $text_point[2]) / 2 ) + $this->_imagewidth / 2);
						$text_height = floor($this->_text_height[$i] - $base_height + (($text_point[7] - $text_point[1]) / 2 ) + $this->_imageheight / 2);
						break;
				}

				imagefttext($image, $this->_text_size[$i], '0', $text_width, $text_height, $font_color, $this->_text_font[$i], $this->_text[$i]);
			}
		}
		return $image;
	}
}
