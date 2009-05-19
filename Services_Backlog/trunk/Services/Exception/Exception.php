<?php
/*
 * Services_Backlogの例外クラス
 * PHP5以上対象
 *
 * @package Services_Backlog
 * @author  devworks <smoochynet@gmail.com>
 * @access  public
 * @version Release: 0.10.0
 * @uses    XML_RPC
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
