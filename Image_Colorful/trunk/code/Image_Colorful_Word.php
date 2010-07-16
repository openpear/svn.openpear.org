<?php
/**
 * Class for Image_Colorful_Word
 *
 * LICENSE:New BSD Licence
 *
 * Copyright (c) 2010, Masahiro Saito
 * All rights reserved.
 *
 * @package     Image_Colorful
 * @author      Masahiro Saito <camelmasa@gmail.com>
 * @copyright   2010 Masahiro Saito
 * @license     http://creativecommons.org/licenses/BSD/
 * @version     0.1.5-stable
 * @link        http://camelmasa.com/
 */

require_once('Image/Colorful.php');

class Image_Colorful_Word extends Colorful
{

    var $_splitheight = 1;

    var $_splitwidth = 1;

    var $_addred = 0;

    var $_addgreen = 0;

    var $_addblue  = 0;

    var $_setred = null;

    var $_setgreen = null;

    var $_setblue  = null;

    var $_text =  '';

    var $_text_size = '';

    var $_text_font = '';

    var $_text_color = '';

    var $_text_point = array();

    var $_space_top = 0;

    var $_space_right = 0;

    var $_space_bottom = 0;

    var $_space_left = 0;

    var $_error_message = "";

    var $_flag = true;


    function Image_Colorful_Word($text, $font, $size, $space = array(0,0,0,0), $color = array('255','255','255'), $split_width = 1, $split_height = 1 )
    {
        $this->checkCharacter($text);
        $this->checkFont($font);
        $this->checkNumber($size);
        $this->checkNumber($space[0]);
        $this->checkNumber($space[1]);
        $this->checkNumber($space[2]);
        $this->checkNumber($space[3]);
        $this->checkColor($color[0]);
        $this->checkColor($color[1]);
        $this->checkColor($color[2]);
        $this->checkNumber($split_width);
        $this->checkNumber($split_height);

        $this->_text = $text; 
        $this->_text_font = $font;
        $this->_text_size = $size; 
        $this->_text_color = $color; 
        $this->_space_top = $space[0]; 
        $this->_space_right = $space[1]; 
        $this->_space_bottom = $space[2]; 
        $this->_space_left = $space[3]; 
        $this->_splitheight = $split_height; 
        $this->_splitwidth = $split_width; 
    }

    function getGenerateImage($image_type = 'gif'){
        $this->checkError();
        $image = $this->GenerateImageWord();

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
        $image = $this->GenerateImageWord();

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

    function GenerateImageWord(){
        $this->checkError();

        $image = imagecreatetruecolor(1, 1);
        $font_color = imagecolorallocate($image, $this->_text_color[0], $this->_text_color[1], $this->_text_color[2]);
        $text_point = imagefttext($image, $this->_text_size, '0', '0', '0', $font_color, $this->_text_font, $this->_text);
        //print_r($text_point);exit;

        $text_width = $text_point[2] - $text_point[0] + $this->_space_right + $this->_space_left;
        $text_height = $text_point[3] - $text_point[5] + $this->_space_top + $this->_space_bottom;

        $base_width = $text_point[6];
        $base_height = $text_point[7];

        $image = imagecreatetruecolor($text_width, $text_height);
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
                imagefilledpolygon($image, array(floor((($text_width / $this->_splitwidth) * ($j))), floor((($text_height / $this->_splitheight) * ($i))),
                            floor(($text_width / $this->_splitwidth) * ($j + 1)), floor((($text_height / $this->_splitheight) * ($i))),
                            floor(($text_width / $this->_splitwidth) * ($j + 1)),floor(($text_height / $this->_splitheight) * ($i + 1)),
                            floor(($text_width / $this->_splitwidth) * ($j)), floor(($text_height / $this->_splitheight) * ($i + 1))), 4, $color);
            }
        }

        imagefttext($image, $this->_text_size, '0', - $base_width + $this->_space_left, - $base_height + $this->_space_top, $font_color, $this->_text_font, $this->_text);

        return $image;
    }
}
