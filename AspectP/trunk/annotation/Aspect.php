<?php
/*
 * Aspect
 *
 * @package annotation
 * @author  localdisk <smoochyinfo@gmail.com>
 * @author  devworks  <smoochynet@gmail.com>
 * @access  public
 * @version Release:  0.10.0
 */
class Aspect {
    private $interCepters = array();
    /**
     * コンストラクタ
     */
    public function __construct() {
        ;
    }

    /**
     * インターセプターを取得します
     *
     * @return array
     */
    public function getInterCepters() {
        return $this->interCepters;
    }

    /**
     * インターセプターを設定します
     *
     * @param object $interCepter
     * @param string $joinPoint
     */
    public function setInterCepters($interCepter, $joinPoint) {
        $this->interCepters[$joinPoint] = $interCepter;
    }
}