<?php
/**
 * Acme_IdolMaster
 *
 * @author  yohei.kawano@gmail.com
 * @package openpear
 * @version $Id$
 */

class Acme_IdolMaster implements Iterator{
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
    private $position = 0;
    private $current_member = false;

    public function __construct() {
        foreach ($this->memberNames as $member) {
            require_once 'Acme/IdolMaster/Member/' . $member . '.php';
            $class = 'Acme_IdolMaster_Member_'. $member;
            $this->members[$member] = new $class();
        }
    }
	
	
//メンバーを指定
    public function select($memberName=false){
		if(isset($this->members[$memberName])){
			$this->current_member=$memberName;
			return true;
		}elseif($memberName==false){
			$this->current_member=false;
		}else{
			throw new Exception('member not found');
		}
	}
	
//マジックメソッド__get
    public function __get($memberName) {
		return $this->get($memberName);
	}

//メンバーを取得	メンバー指定後であれば要素を取得
    public function get($memberName=false) {
		//メンバー指定があれば$memberNameのかわりに$propertyが来る
		if($this->current_member){
			if($memberName){
				return $this->members[$this->current_member]->{$memberName};
			}else{
				return $this->members[$this->current_member];
			}
		}
		//引数のメンバー取得
		if(isset($this->members[$memberName])){
			return $this->members[$memberName];
		}else{
			throw new Exception('member not found');
		}
	}

//マジックメソッド__set
    public function __set($property,$value) {
		return $this->set($property,$value);
	}
//値ゲット
    public function set($property,$value) {
		//メンバそのものは追記不可
		if(!$this->current_member){ return false;}
		//セット
		return $this->members[$this->current_member]->set($property,$value);
	}

//マジックメソッド__call
    function __call($method, $args){
        if(empty($args)) { return $this->get($method); }
        elseif(isset($args[0])) { return $this->set($method,$args[0]); }
        return null;
    }

//イテレータ
    public function current(){
	 return $this->members[$this->memberNames[$this->position]]; }
    public function valid(){
		return isset($this->members[$this->memberNames[$this->position]]);
	}
    public function key(){ return $this->position; }
    public function next(){ $this->position++; }
    public function rewind(){ $this->position=0; }
	
	

}
