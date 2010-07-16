<?php
/**
 * Class for Colorful 
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

class Colorful
{

    function addColors($red, $green, $blue){
        $this->checkError();
        $this->checkColors($red);
        $this->checkColors($green);
        $this->checkColors($blue);
        $this->_addred = $red;
        $this->_addgreen = $green;
        $this->_addblue = $blue;
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

    function setColors($red, $green, $blue){
        $this->checkError();
        $this->checkColors($red);
        $this->checkColors($green);
        $this->checkColors($blue);
        $this->_setred = $red;
        $this->_setgreen = $green;
        $this->_setblue = $blue;
    }

    function setRed($red){
        $this->checkError();
        $this->checkColor($red);
        $this->_setred = $red;
    }

    function setGreen($green){
        $this->checkError();
        $this->checkColor($green);
        $this->_setgreen = $green;
    }

    function setBlue($blue){
        $this->checkError();
        $this->checkColor($blue);
        $this->_setblue = $blue;
    }

    function checkError(){
        if($this->_flag == false){
            $this->_error_message = "Image_Colorful function error";
            $this->showError();
        }
    }

    function checkColor($color){
        if(!preg_match('/^[0-9]+$/',$color) || ( $color <= 255 && $color >= -255)) {
            $this->_error_message = "The color is from -255 to 255 ranges";
            $this->showError();
        }

    }

    function checkColors($color){
        if(!preg_match('/^[0-9]+$/',$color) || ($color <= 255 && $color >= 0)) {
            $this->_error_message = "The colors is from 0 to 255 ranges";
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
        } else if(!file_exists($font)){
            $this->_error_message = "No font file";
            $this->showError();
        }

    }

    function checkPoint($point){
        switch($point){
            case 'UL':
            case 'UPPER_LEFT':
                $return = "upper_left";
                break;
            case 'UR':
            case 'UPPER_RIGHT':
                $return = "upper_right";
                break;
            case 'DL':
            case 'LEFT_LOWER':
                $return = "left_lower";
                break;
            case 'DR':
            case 'RIGHT_LOWER':
                $return = "right_lower";
                break;
            case 'C':
            case 'CENTER':
                $return = "center";
                break;
            default:
                $this->_flag = false;
                return false;
                break;
        }
        return $return;
    }

    function showError(){
        $this->_flag = false;
        return false;
    }
    function getErrorMessage(){
        return $this->_error_message;
    }
}
