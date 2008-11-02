<?php

class Holo_Image extends Imagick
{
  public function resizeProportionally($column, $rows)
  {
    $width = $this->getImageWidth();
    $height = $this->getImageHeight();
    $scale_w = $column / $width;
    $scale_h = $rows / $height;

    $scale = ($scale_w < $scale_h) ? $scale_w : $scale_h;
    $this->ResizeImage(round($width * $scale) , round($height * $scale),imagick::FILTER_SINC ,1);
  }
  
  public function resizeAndCrop($column, $row)
  {
    $width = $this->getImageWidth();
    $height = $this->getImageHeight();
    $scale_w = $column / $width;
    $scale_h = $row / $height;

    $scale = ($scale_w < $scale_h) ? $scale_h : $scale_w;
    $this->thumbnailImage(round($width * $scale), round($height * $scale));
    
    $width = $this->getImageWidth();
    $height = $this->getImageHeight();
    $x = $width / 2 - $column / 2;
    $y = $height / 2 - $row / 2;
    $this->cropImage($column, $row, $x,  $y);
    $this->setImagePage($column, $row, 0,  0);
  }

  public function action($action, $params)
  {
    $this->resetIterator();
    call_user_func_array(array($this, $action), $params);
    foreach($this as $frame) {
      call_user_func_array(array($frame, $action), $params);
    }
      
    $this->CoalesceImages();
    //$this->resetIterator();
  }

  public function write($filename)
  {
    if ($this->getNumberImages() > 1) {
      $this->writeImages($filename, true);
    } else {
      $this->writeImage($filename);
    }
  }

}
