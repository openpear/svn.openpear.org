<?php
/**
 * @package File_HyperEstraier
 */
/**
 * HyperEstraier document draft parser.
 */
class File_HyperEstraier_Draft {
	private $attrs = array();
	private $kwords = array();
	private $socre = array();
	private $shadow = array();
	
	protected $texts = array();
	private $hiddens = array();
	
	/**
	 * Load draft data.
	 * @param string $string Document draft string.
	 * @return boolean True on success.
	 */
	function load($string){
		$attrs = array();
		$kwords = array();
		$score = array();
		$shadow = array();
		
		$hiddens = array();
		$texts = array();
		
		// detect EOL
		$eol = "\n";
		if(strpos($string, "\r\n")){
			$eol = "\r\n";
		}elseif(strpos($string, "\r")){
			$eol = "\r";
		}
		
		$is_header = true;
		foreach(explode($eol, $string) as $line){
			if(strlen($line)==0){
				$is_header = false;
				continue;
			}
			if($is_header){
				if($line[0]=='%'){
					if(substr($line,0,7)=='%VECTOR'){
						$kv=explode("\t",$line);
						for($i=0;$i<(count($kv)-1)/2;$i++){
							$kwords[$kv[2*$i+1]]=$kv[2*$i+2];
						}
					}elseif(substr($line,0,6)=='%SCORE'){
						$kv=explode("\t",$line);
						$score=$kv[1];
					}elseif(substr($line,0,7)=='%SHADOW'){
						$kvs=explode("\t",$line);
						$shadow[$kvs[1]]=$kvs[2];
					}else{
						trigger_error("The library does not know the header. ".$line, E_USER_NOTICE);
						return false;
					}
					continue;
				}
				$kv=explode('=',$line,2);
				if(isset($kv[1])){
					$attrs[$kv[0]]=$kv[1];
				}else{
					trigger_error("Parse error in hyperestraier document draft header.", E_USER_NOTICE);
					return false;
				}
			}else{
				if($line[0]=="\t"){ // hidden text
					$hiddens[] = substr($line, 1);
				}else{
					$texts[] = $line;
				}
			}
		}
		
		$this->attrs = $attrs;
		$this->kwords = $kwords;
		$this->score = $score;
		$this->shadow = $shadow;
		
		$this->hiddens = $hiddens;
		$this->texts = $texts;
		
		return true;
	}
	
	/**
	 * Dump draft data.
	 * @return string Document draft
	 */
	function dump(){
		trigger_error("Not implemented yet.", E_USER_ERROR);
	}
	
	/**
	 * Get a list of attribute names.
	 * @return array List of attribute names.
	 */
	function getAttributeNames(){
		return keys($this->attrs);
	}
	
	/**
	 * Get the value of an attribute.
	 * @param string $name attribute name.
	 * @return string The value.
	 */
	function getAttribute($name){
		if(isset($this->attrs[$name])){
			return $this->attrs[$name];
		}else{
			return false;
		}
	}
	
	/**
	 * Set the value of an attribute.
	 * @param string $name attribute name.
	 * @param string $value attribute value.
	 */
	function setAttribute($name, $value){
		$this->attrs[$name]=$value;
	}
	
	/**
	 * Add a text.
	 * @param string $text The text to add.
	 * @param boolean $hidden 
	 */
	function addText($text, $hidden=false){
		if($hidden){
			$this->hiddens[]=$text;
		}else{
			$this->texts[]=$text;
		}
	}
	
	/**
	 * Get document part in one string
	 * @return string Document part.
	 */
	function getDocument(){
		$texts = $this->texts;
		foreach($hiddens as $h){
			$texts[] = "\t".$h;
		}
		return join("\n", $texts);
	}
}
