<?php
require_once 'PHP/Obfuscator/Filter/Filter.php';
class PHP_Obfuscator_Filter_ExpireRestrictionFilter implements PHP_Obfuscator_Filter_Filter
{
    private $expire = null;
    public function setArgs(array $args) {
        if (!isset($args[0])) {
            throw new Exception("parameter for ExpireRestrictionFilter is not assigned");
        }
        $expire = null;
        try {
            $expire = new DateTime($args[0]);
        } catch (Exception $e) {
            throw new Exception("parameter for ExpireRestrictionFilter is invalid : " . $args[0]);
        }
        $expire->setTimezone(new DateTimeZone('UTC'));
        $this->expire = $expire;
    }
    public function getCode() {
        return 'if (gmdate(\'YmdHis\') > \'' . $this->expire->format('YmdHis') . '\') { return; } ';
    }
}
