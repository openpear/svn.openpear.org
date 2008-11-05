<?php
/**
 * Acme_IdolMaster_Member_Base
 *
 * @author  yohei.kawano@gmail.com
 * @package openpear
 * @version $Id
 */

class Acme_IdolMaster_Member_Base
{
    private $name_ja;
    private $first_name_ja;
    private $family_name_ja;
    private $name_en;
    private $first_name_en;
    private $family_name_en;
    private $nick;
    private $birthday;
    private $age;
    private $blood_type;

    function __construct(){
        //$this->birthday(new DateTime($this->birthday));
        $this->name_ja($this->family_name_ja() . $this->first_name_ja());
        $this->name_en($this->first_name_en() . ' ' . $this->family_name_en());
    }

    function __call($method, $args){
        if(empty($args) && isset($this->{$method})) return $this->{$method};
        else if(isset($args[0])) return $this->{$method} = $args[0];
        return null;
    }
}
