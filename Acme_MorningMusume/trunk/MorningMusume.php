<?php
/**
 * Acme_MorningMusume
 *
 * @author  riaf<riaf@nequal.jp>
 * @package openpear
 * @version $Id$
 */
require_once 'Acme/MorningMusume/Member/Base.php';

class Acme_MorningMusume
{
    public $members = array();
    private $memberNames = array(
        'FukudaAsuka',
        'NakazawaYuko',
        'IidaKaori',
        'AbeNatsumi',
        'IshiguroAya',
        'IchiiSayaka',
        'YaguchiMari',
        'YasudaKei',
        'GotohMaki',
        'IshikawaRika',
        'YoshizawaHitomi',
        'TsujiNozomi',
        'KagoAi',
        'TakahashiAi',
        'KonnoAsami',
        'OgawaMakoto',
        'NiigakiRisa',
        'KameiEri',
        'TanakaReina',
        'MichishigeSayumi',
        'FujimotoMiki',
        'KusumiKoharu',
        'MitsuiAika',
        'LiChun',
        'QianLin',
    );

    public function __construct(){
        foreach($this->memberNames as $member){
            require_once 'Acme/MorningMusume/Member/'. $member. '.php';
            $class = 'Acme_MorningMusume_Member_'. $member;
            $this->members[] = new $class();
        }
    }

    public function sort(){
        throw new Exception('><');
    }
    public function select(){
        throw new Exception('><');
    }
}
