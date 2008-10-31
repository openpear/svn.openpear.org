<?php
/**
 * Acme_MorningMusume_Member_Base
 *
 * @author  riaf<riaf@nequal.jp>
 * @package openpear
 * @version $Id$
 */

class Acme_MorningMusume_Member_Base
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
    private $hometown;
    private $emoticon;
    private $class;
    private $graduate_date;
    private $active = false;

    function __construct(){
        $this->birthday(new DateTime($this->birthday));
        if(is_null($this->graduate_date)) $this->active(true);
        else $this->graduate_date(new DateTime($this->graduate_date));
        $this->name_ja($this->family_name_ja(). $this->first_name_ja());
        $this->name_en($this->first_name_en(). ' '. $this->family_name_en());
        $this->age($this->_calculate_age());
    }

    function __call($method, $args){
        if(empty($args) && isset($this->{$method})) return $this->{$method};
        else if(isset($args[0])) return $this->{$method} = $args[0];
        return null;
    }

    function _calculate_age(){
        $today = new DateTime();
        if(($today->format('n') - $this->birthday->format('n')) >= 0){
            if(($today->format('j') - $this->birthday->format('j')  ) >= 0){
                return $today->format('j') - $this->birthday->format('Y');
            } else {
                return ($today->format('j') - $this->birthday->format('j')) - 1;
            }
        } else {
            return ($today->format('j') - $this->birthday->format('j')) - 1;
        }
    }
}
