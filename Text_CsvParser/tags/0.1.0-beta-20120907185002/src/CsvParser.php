<?php
/**
 * read and parse a CSV file
 *
 * usage:
 *   <code>
 *   $csv = new CsvParser('my_file.csv');
 *   if ($csv->hasError()) {
 *     // nice error handler ...
 *     die;
 *   }
 *
 *   while ($array = $csv->getNext()) {
 *     print_r($array);
 *   }
 *   </code>
 * 
 *
 * 
 *
 * Created: 2007-11-12
 * Last update: 2007-11-20
 *
 * @link 
 * @copyright 
 * @author OKUMURA Yoshifumi 
 * @package csvparser
 * @version $Id$
 */
define('MAX_LINE_SIZE', 16384);
define('END_OF_FILE', "\x1A");
/**
 * CSV parser
 *
 * @package csvparser
 * @version $Id$
 * @author OKUMURA Yoshifumi
 */
class CsvParser {
	/**
	 * @access private
	 */
	var $fp;

	/**
	 * @access private
	 */
	var $_error;

	/**
	 * @access private
	 */
	var $_header;

	/**
	 * @access private
	 */
	var $_filename;

	/**
	 * @access private
	 */
	var $_file_encoding;
	var $_internal_encoding;
	var $_total;
	var $_readed;
	var $_line_no;
	var $_raw_data;
	var $_eof;
	var $_pointer;

	/**
	 *
	 * @static
	 * @access public
	 * @param string $filename ファイル名
	 * @param boolean $has_header (optional) 1行目がヘッダのときはTRUEにする
	 * @param string $encoding
	 * @return CsvFile
	 */
	function CsvParser($filename, $has_header = FALSE, $encoding = 'Windows-31J') {
		$this->_error             = NULL;
		$this->_header            = NULL;
		$this->_filename          = $filename;
		$this->_file_encoding     = $encoding;
		$this->_internal_encoding = mb_internal_encoding();
		$this->_total      = 0;
		$this->_readed     = 0;
		$this->_line_no    = 0;
		$this->_eof        = FALSE;

		if (($size = filesize($filename)) == FALSE) {
			$this->_error = sprintf('%s: cannot get size', $filename);
		}
		$this->_total = $size;

		if (! $this->openfile()) return;

		if ($has_header) {
			$this->_header = $this->readLine();
		}
	}

	/**
	 *
	 * @access public
	 * @return boolean
	 */
	function hasError() {
		return !empty($this->_error);
	}

	/**
	 *
	 * @return string
	 */
	function getError() {
		return $this->_error;
	}
	
	/**
	 * ファイルサイズを返す
	 * @return integer
	 */
	function getSize() {
		return $this->_total;
	}

	/**
	 * 読み込み済みのバイト数を返す
	 * @return integer
	 */
	function getReadedSize() {
		return $this->_readed;
	}

	/**
	 *
	 * @return string
	 */
	function getFileName() {
		return $this->_filename;
	}

	/**
	 *
	 *
	 * @access public
	 * @return array
	 */
	function getNext() {
		return $this->readLine();
	}

	/**
	 * 行番号を返す
	 *
	 * @return integer
	 */
	function getLineNo() {
		return $this->_line_no;
	}

	/**
	 * パース前のデータを返す
	 * getNext()が呼ばれたあとじゃないとダメ
	 *
	 * @return string
	 */
	function getRawData() {
		return $this->_raw_data;
	}


	/**
	 *
	 * @return boolean
	 */
	function eof() {
		return $this->_eof;
	}

	/**
	 * ヘッダをキーとして連想配列を返す
	 *
	 * @return array
	 */
	function getNextAssoc() {
		$line = $this->getNext();

		if (empty($this->_header) || empty($line)) return $line;

		$result = array();
		$count = count($this->_header);
		for ($i = 0; $i < $count; $i++) {
			$result[$this->_header[$i]] = array_shift($line);
		}
		return $result;
	}

	/**
	 *
	 * @access private
	 */
	function readLine() {
		if (!is_resource($this->fp)) {
			$this->_eof = TRUE;
			return NULL;
		}

		if (feof($this->fp)) {
			fclose($this->fp);
			$this->fp = NULL;
			$this->_eof = TRUE;
			return NULL;
		}

		$this->_line_no++;
		$line = fgets($this->fp, MAX_LINE_SIZE);
		$this->_raw_data = $line;

		if ($line == END_OF_FILE) {
			$this->_eof = TRUE;
			return NULL;
		}

		if (strlen($line) == 0) return NULL;

		while ((substr_count($line, '"') % 2) == 1 && !feof($this->fp)) {
			$line .= fgets($this->fp, MAX_LINE_SIZE);
		}

		$this->_readed += strlen($line);

		// #getRawData()のため
		$this->_raw_data = $line;

		mb_convert_variables($this->_internal_encoding, $this->_file_encoding, $line);

		$line = preg_replace('/(?:\x0D\x0A|[\x0D\x0A])?$/', '', $line);
		$line .= ',';
		if (preg_match_all('/("[^"]*(?:""[^"]*)*"|[^,]*),/', $line, $matches) == 0) {
			$this->_error = 'cannot parse line';
			return NULL;
		}
		
		return array_map(array(&$this, 'dequote'), $matches[1]);
	}

	/**
	 * remove double quote
	 * @access private
	 */
	function dequote($column) {
		if (preg_match('/^"(.*)"$/s', $column, $matches)) {
			return str_replace('""', '"', $matches[1]);
		}
		else {
			return $column;
		}
	}

	/**
	 *
	 */
	function openfile() {
		if (($this->fp = fopen($this->_filename, 'r')) === FALSE) {
			$this->_error = sprintf('%s: cannot open', $this->_filename);
			return FALSE;
		}
		return TRUE;
	}

	/**
	 *
	 */
	function __sleep() {
		if (is_resource($this->fp)) {
			$this->_pointer = ftell($this->fp);
			fclose($this->fp);
		}
		return array('fp',
			     '_error',
			     '_header',
			     '_filename',
			     '_file_encoding',
			     '_internal_encoding',
			     '_total',
			     '_readed',
			     '_line_no',
			     '_raw_data',
			     '_eof',
			     '_pointer',
			     );
	}

	/**
	 *
	 */
	function __wakeup() {
		if ($this->openfile()) {
			fseek($this->fp, $this->_pointer);
		}
		return;
	}
}
