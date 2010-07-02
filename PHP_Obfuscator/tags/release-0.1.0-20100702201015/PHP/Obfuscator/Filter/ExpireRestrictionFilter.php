<?php
require_once 'PHP/Obfuscator/Filter/Filter.php';
class PHP_Obfuscator_Filter_ExpireRestrictionFilter implements PHP_Obfuscator_Filter_Filter
{
    private $expire = null;
    public function __construct(DateTime $expire) {
        $expire->setTimezone(new DateTimeZone('UTC'));
        $this->expire = $expire;
    }
    public function getCode() {
        return 'if (gmdate(\'YmdHis\') > \'' . $this->expire->format('YmdHis') . '\') { return; } ';
    }
}
