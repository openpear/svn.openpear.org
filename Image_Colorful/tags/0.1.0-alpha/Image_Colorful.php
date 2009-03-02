<?php
/**
 * Class for colorful generator
 *
 * LICENSE:
 *
 * Copyright (c) 2009, Masahiro Saito
 * All rights reserved.
 *
 * @package     Image_Colorful
 * @author      Masahiro Saito <camelmasa@gmail.com>
 * @copyright   2009 Masahiro Saito
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
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

	var $_author;

	var $_author_size  = 0;

	var $_author_font;

	var $_author_width  = 0;

	var $_author_height  = 0;

	var $_author_color_red;

	var $_author_color_green;

	var $_author_color_blue;

	var $_author_color_flag;

	var $_title_size  = 0;

	var $_title_font;

	var $_title_width  = 0;

	var $_title_height  = 0;

	var $_title_color_red;

	var $_title_color_green;

	var $_title_color_blue;

	var $_title_color_flag;

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

	function setAuthor($author, $font, $size, $width=0, $height=0){
		$this->checkError();
		$this->checkChracter($author);
		$this->checkFont($font);
		$this->checkNumber($size);
		$this->checkNumber($width);
		$this->checkNumber($height);

		$this->_author = $author;
		$this->_author_font = $font;
		$this->_author_size = $size;
		$this->_author_width = $width;
		$this->_author_height = $height;
	}

	function setAuthorColor($red, $green, $blue){
		$this->checkError();
		$this->checkColor($red);
		$this->checkColor($green);
		$this->checkColor($blue);

		$this->_author_color_red = $red;
		$this->_author_color_green = $green;
		$this->_author_color_blue = $blue;
		$this->_author_color_flag = true;
	}

	function setTitle($title, $font, $size, $width=0, $height=0){
		$this->checkError();
		$this->checkChracter($title);
		$this->checkFont($font);
		$this->checkNumber($size);
		$this->checkNumber($width);
		$this->checkNumber($height);

		$this->_title = $title;
		$this->_title_font = $font;
		$this->_title_size = $size;
		$this->_title_width = $width;
		$this->_title_height = $height;
	}

	function setTitleColor($red, $green, $blue){
		$this->checkError();
		$this->checkColor($red);
		$this->checkColor($green);
		$this->checkColor($blue);

		$this->_title_color_red = $red;
		$this->_title_color_green = $green;
		$this->_title_color_blue = $blue;
		$this->_title_color_flag = true;
	}



	function getGenerateImage($image_type = 'gif'){
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

		//setAuthor
		if(!$this->_author_color_flag){
			$author_font_color = imagecolorallocate($image, 255, 255, 255);
		} else {
			$author_font_color = imagecolorallocate($image, $this->_author_color_red,  $this->_author_color_green, $this->_author_color_blue);
		}
		imagefttext($image, $this->_author_size, '0', $this->_author_width, $this->_author_height, $author_font_color, $this->_author_font, $this->_author);

		//setTitle
		if(!$this->_title_color_flag){
			$title_font_color = imagecolorallocate($image, 255, 255, 255);
		} else {
			$title_font_color = imagecolorallocate($image, $this->_title_color_red,  $this->_title_color_green, $this->_title_color_blue);
		}
		imagefttext($image, $this->_title_size, '0', $this->_title_width, $this->_title_height, $title_font_color, $this->_title_font, $this->_title);

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

	function checkError(){
		if($this->flag == false){
			$this->error_message = "Image_Colorful function error";
			$this->showError();
		}
	}

	function checkColor($color){
		if(!preg_match('/^[0-9]+$/',$number) || $color <= 255 && $color >= -255) {
			$this->error_message = "The color is from -255 from -255 to 255 ranges";
			$this->showError();
		}

	}

	function checkNumber($number){
		if(!preg_match('/^[0-9]+$/',$number)) {
			$this->error_message = "No number error";
			$this->showError();
		}

	}

	function checkChracter($char){
		if(!$char) {
			$this->error_message = "No message error";
			$this->showError();
		}

	}

	function checkFont($font){
		if(!$font) {
			$this->error_message = "No font error";
			$this->showError();
		}

	}
	function showError(){
		$this->flag = false;
		return false;
	}

	function getErrorMessage(){
		return $this->error_message;
	}



}
