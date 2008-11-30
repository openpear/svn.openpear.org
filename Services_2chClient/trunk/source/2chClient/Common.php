<?php

class Services_2chClient_Common
{
    /**
     * ユーザーエージェント
     */
    protected $_userAgent = 'Monazilla/1.00 (Services_2chClient)';
    /**
     * 最終更新日時
     */
    protected $_lastModified;

    function setProperty($name, $value){
        $this->'_'.$name = $value;
    }

    function getProperty($name){
        return $this->'_'.$name;
    }
}