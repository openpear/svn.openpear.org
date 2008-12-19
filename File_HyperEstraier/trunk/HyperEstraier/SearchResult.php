<?php
/**
 * @package File_HyperEstraier
 */
require_once('File/HyperEstraier/Snippet.php');
/**
 * HyperEstraier search result parser.
 * 
 * This class parse the output of estmaster search result.
 */
class File_HyperEstraier_SearchResult implements SeekableIterator {
	private $docs = array();
	private $hint = array();
	private $pos = 0;
	
	/**
	 * constructor
	 *
	 * @param string $string Document draft string.
	 */
	function __construct($string=''){
		if($string){
			$this->load($string);
		}
	}
	
	/**
	 * Load draft data.
	 * @param string $string Search result string.
	 * @return boolean True on success.
	 */
	function load($string){
		$lines=explode("\n", $string);
		if(!isset($lines[0])){ return null; }
		$separator=$lines[0];
		$parts_str=explode($separator, $string);
		$ct=0;
		foreach($parts_str as $str){
			if(strpos($str,':END')===0){ break; }
			$str=substr($str,1);
			if($ct==0){
				// always empty because this is the very beginning of the document part.
			}elseif($ct==1){
				// meta part
				$lines=explode("\n",$str);
				foreach($lines as $line){
					if(!$line){ continue; }
					$kv=explode("\t",$line,2);
					$this->hint[$kv[0]]=$kv[1];
				}
			}else{
				// snippet part
				$snippet = new File_HyperEstraier_Snippet();
				$snippet->load($str);
 				$this->docs[] = $snippet;
			}
			$ct++;
		}
		return true;
	}
	
	/** Iterator */
	function current(){
		return current($this->docs);
	}
	
	/** Iterator */
	function key(){
		return $this->pos;
	}
	
	/** Iterator */
	function next(){
		$this->pos++;
	}
	
	/** Iterator */
	function rewind(){
		$this->pos=0;
	}
	
	/** Iterator */
	function valid(){
		if($this->pos < count($this->docs)){
			return true;
		}
		return false;
	}
	
	/** SeekableIterator */
	function seek($index){
		$this->rewind();
		$position = 0;
		while($position < $index && $this->valid()) {
			$this->next();
			$position++;
		}
		if (!$this->valid()) {
			throw new OutOfBoundsException('Invalid seek position');
		}
	}
}
