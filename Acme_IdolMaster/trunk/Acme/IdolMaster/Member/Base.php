<?php
/**
 * Acme_IdolMaster_Member_Base
 *
 * @author  yohei.kawano@gmail.com
 * @package openpear
 * @version $Id$
 */

class Acme_IdolMaster_Member_Base
{
    protected $name_ja;
    protected $first_name_ja;
    protected $family_name_ja;
    protected $name_en;
    protected $first_name_en;
    protected $family_name_en;
    protected $nick;
    protected $birthday;
    protected $age;
    protected $blood_type;

    function __construct(){
        //$this->birthday(new DateTime($this->birthday));
        $this->name_ja($this->family_name_ja() . $this->first_name_ja());
        $this->name_en($this->first_name_en() . ' ' . $this->family_name_en());
    }

    function __call($method, $args){
        if(empty($args) && isset($this->{$method})) { return $this->{$method}; }
        else if(isset($args[0])) { return $this->set($method,$args[0]); }
        return null;
    }

	//マジックメソッド__get
	    public function __get($property) {
			return $this->get($property);
		}

	//値ゲット
	    public function get($property) {
			if(isset($this->{$property})){
				return $this->{$property};
			}else{
				return false;
			}
		}

	//マジックメソッド__set
	    public function __set($property,$value) {
			return $this->set($property,$value);
		}

	//値セット
	    public function set($property,$value) {
			/*
				宣言されてるやつだけチェックの方法がわかんない
				issetだとnullを代入すると変更不能になる
			*/
				//値を変更
				$this->{$property}=$value;
				
				//姓、名の場合は氏名も変更
				if(    $property==='family_name_ja'
					|| $property==='first_name_ja'
					|| $property==='family_name_en'
					|| $property==='first_name_en'
				){
					$this->__construct();
				}
			return true;
		}


}
