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
 * @version     0.1.0-alpha
 * @link        http://camelmasa.com/
 */


class Image_Colorful
{

	var $_imageheight;

	var $_imagewidth;

	var $_splitheight;

	var $_splitwidth;

	var $_addred   = 0;

	var $_addgreen = 0;

	var $_addblue  = 0;

	var $_text =  array();

	var $_text_size = array();

	var $_text_font = array();

	var $_text_width = array();

	var $_text_height = array();

	var $_text_color = array();

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

	function addRed($red){
		$this->checkError();
		$this->checkColor($red);
		$this->_addred = $red;
	}

	function addGreen($green){
		$this->checkError();
		$this->checkColor($green);
		$this->_addgreen = $green;
	}

	function addBlue($blue){
		$this->checkError();
		$this->checkColor($blue);
		$this->_addblue = $blue;
	}

	function addTexts($text, $font, $size, $width=0, $height=0, $color=array(255,255,255)){
		$this->checkError();
		$this->checkCharacter($text);
		$this->checkFont($font);
		$this->checkNumber($size);
		$this->checkNumber($width);
		$this->checkNumber($height);
		$this->checkColor($color[0]);
		$this->checkColor($color[1]);
		$this->checkColor($color[2]);

		$this->_text[] = $text;
		$this->_text_font[] = $font;
		$this->_text_size[] = $size;
		$this->_text_width[] = $width;
		$this->_text_height[] = $height;
		$this->_text_color[] = $color;
	}

	/*
	function getOdds(){
		$this->checkError();
		$this->_splitheight;
		$this->_splitwidth;
		$this->_addred;
		$this->_addgreen;
		$this->_addblue;

	}*/

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
		imagecolorallocate($image, 0, 0, 0);
		for($i=0;$i<$this->_splitheight;$i++){
			for($j=0;$j<$this->_splitwidth;$j++){

				if($this->_addred > 0){
					$min_red = $this->_addred;
					$max_red = 255;
				} else {
					$min_red = 0;
					$max_red = 255 + $this->_addred;
				}

				if($this->_addgreen > 0){
					$min_green = $this->_addgreen;
					$max_green = 255;
				} else {
					$min_green = 0;
					$max_green = 255 + $this->_addgreen;
				}

				if($this->_addblue > 0){
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

		//addTexts
		if(count($this->_text)){
			for($i=0;$i<count($this->_text);$i++){
				$font_color = imagecolorallocate($image, $this->_text_color[$i][0], $this->_text_color[$i][1], $this->_text_color[$i][2]);
				imagefttext($image, $this->_text_size[$i], '0', $this->_text_width[$i], $this->_text_height[$i], $font_color, $this->_text_font[$i], $this->_text[$i]);
			}
		}
		return $image;
	}

	function checkError(){
		if($this->_flag == false){
			$this->_error_message = "Image_Colorful function error";
			$this->showError();
		}
	}

	function checkColor($color){
		if(!preg_match('/^[0-9]+$/',$color) || $color <= 255 && $color >= -255) {
			$this->_error_message = "The color is from -255 from -255 to 255 ranges";
			$this->showError();
		}

	}

	function checkNumber($number){
		if(!preg_match('/^[0-9]+$/',$number)) {
			$this->_error_message = "No number error";
			$this->showError();
		}

	}

	function checkCharacter($char){
		if(!$char) {
			$this->_error_message = "No message error";
			$this->showError();
		}

	}

	function checkFont($font){
		if(!$font) {
			$this->_error_message = "No font error";
			$this->showError();
		}

	}
	function showError(){
		$this->_flag = false;
		return false;
	}

	function getErrorMessage(){
		return $this->_error_message;
	}
}
