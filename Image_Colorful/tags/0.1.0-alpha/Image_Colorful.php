<?php
/**
 * Class for colorful generator
 *
 * LICENSE:
 *
 * Copyright (c) 2009, Masahiro Saito
 * All rights reserved.
 *
 * @package     Colorful_Generator
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

  var $_flag = true;

  var $_content_type = "image/gif";

  function Image_Colorful($height = '', $width = '', $split_height = 1, $split_width = 1 )
  {
    if(!preg_match('/^[0-9]+$/',$width)) {
      $this->showError();
    }
    if(!preg_match('/^[0-9]+$/',$height)) {
      $this->showError();
    }
    if(!preg_match('/^[0-9]+$/',$split_height)) {
      $this->showError();
    }
    if(!preg_match('/^[0-9]+$/',$split_width)) {
      $this->showError();
    }

    $this->_imageheight = $height; 
    $this->_imagewidth = $width; 
    $this->_splitheight = $split_height; 
    $this->_splitwidth = $split_width; 
  }

  function getGenerateImage(){
    if($this->flag == false){
      $this->showError();
    }
    $image = imagecreatetruecolor($this->_imageheight, $this->_imagewidth);
    imagecolorallocate($image, 0, 0, 0);
    for($i=0;$i<$this->_splitheight;$i++){//height
      for($j=0;$j<$this->_splitwidth;$j++){//width
        $color = imagecolorallocate($image, rand(0,255), rand(0,255), rand(0,255));
        imagefilledpolygon($image, array(floor((($this->_imagewidth / $this->_splitwidth) * ($j))), 0,
                                         floor(($this->_imagewidth / $this->_splitwidth) * ($j + 1)), 0,
                                         floor(($this->_imagewidth / $this->_splitwidth) * ($j + 1)),floor(($this->_imageheight / $this->_splitheight) * ($i + 1)),
                                         floor(($this->_imagewidth / $this->_splitwidth) * ($j)), floor(($this->_imageheight / $this->_splitheight) * ($i + 1))), 4, $color);
      }
    }
    header('Content-type: '.$this->_content_type);
    imagegif($image);
  }

  function showError(){
    $this->flag = false;
    return false;
  }

  function Debug(){
    $this->_content_type = "text/html";
    echo $this->_imageheight."<br>";
    echo $this->_imagewidth."<br>";
    echo $this->_splitheight."<br>";
    echo $this->_splitwidth."<br>";
    echo $this->_flag."<br>";
    echo $this->_content_type."<br>";
    exit;
  }

}
