<?php
/*
 * Services_Backlogの例外クラス
 * PHP5以上対象
 *
 * @package    Services_Backlog
 * @author     devworks <smoochynet@gmail.com>
 */
class Services_Backlog_Exception extends Exception {
    /**
     * コンストラクタ
     * 
     * @param string $message
     * @param integer $code
     */
    public function __construct($message = null, $code = 0) {
        parent::__construct($message, intval($code));
    }
}
?>
