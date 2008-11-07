<?php
/**
 * Acme_IdolMaster
 *
 * @author  yohei.kawano@gmail.com
 * @package openpear
 * @version $Id$
 */

class Acme_IdolMaster {
    public $members = array();
    private $memberNames = array(
            'AmamiHaruka',
            'KisaragiChihaya',
            'HagiwaraYukiho',
            'TakatsukiYayoi',
            'AkizukiRitsuko',
            'MiuraAzusa',
            'MinaseIori',
            'KikuchiMakoto',
            'FutamiAmi',
            'FutamiMami',
            'HoshiiMiki',
            );

    public function __construct() {
        foreach ($this->memberNames as $member) {
            require_once 'Acme/IdolMaster/Member/' . $member . '.php';
            $class = 'Acme_IdolMaster_Member_'. $member;
            $this->members[] = new $class();
        }
    }
}
