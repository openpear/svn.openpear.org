<?php
/**
 * @package File_HyperEstraier
 */
require_once("File/HyperEstraier/Draft.php");
/**
 * HyperEstraier snippet parser.
 */
class File_HyperEstraier_Snippet extends File_HyperEstraier_Draft {
	/**
	 * 
	 */
	function getPlainSnippet(){
		$rows = array();
		foreach($this->texts as $doc){
			$snp=split("\t", $doc , 2);
			$rows[] = $snp[0];
		}
		return join("",$rows);
	}
}
