<?php
/**
 * Text_Pictogram_Mobile_Common - 絵文字操作のベースになる抽象クラス
 *
 * @category Text
 * @package  Text_Pictogram_Mobile
 * @author   Daichi Kamemoto <daikame@gmail.com>
 */
abstract class Text_Pictogram_Mobile_Common
{
	protected
		$carrier = null,
		$pictograms = null,
		$pictogramType = null,
		$characterEncode = 'sjis-win',
		$intercodePrefix = '[({',
		$intercodeSuffix = '})]',
		$convertDatabase = null,
		$escapeSequence = '~',
		$picdbDir = 'data',
		$encoding = null;
	protected static
		$pictogramSeparator = ';';

	/**
	 * Initialize
	 * 絵文字データベースから絵文字を取得し、オブジェクトにセットする
	 */
	public function initialize()
	{
		$this->loadPictograms($this->getCarrier());
		$this->loadConvertDB($this->getCarrier());
	}

	/**
	 * 保持している絵文字のデータをすべてバイナリの配列として返す。
	 * 引数のキャリアは変換の基準となるキャリア。
	 *
	 * 細かいこと面倒な時はこれを使って
	 * <code>
	 * $pict = Text_Pictogram_Mobile::factory($carrier);
	 * $emoji = $pict->getFormattedPictogramsArray();
	 * $smarty->assing("emoji", $emoji);
	 * </code>
	 * とかやってしまう。と、便利。
	 * 
	 * @param string $carrier
	 */
	public function getFormattedPictogramsArray($carrier = 'docomo')
	{
		if ($this->getCarrier() == $carrier) {
			foreach ($this->pictograms[$carrier] as $number => $pictogram) {
				$binaryPictograms[$number] = pack('H*', (string)$pictogram);
			}
		} else {
			$this->loadConvertDB($carrier);
			foreach ($this->convertDatabase[$carrier] as $number => $convertNumbers) {
				$convertNumber = $convertNumbers[$this->getCarrier()];
				$binaryPictograms[$number] = $this->getPictogram($convertNumber);
			}
		}

		return $binaryPictograms;
	}

	/**
	 * getFormattedPictogramsArray() のエイリアス
	 */
	public function getAll($carrier = 'docomo')
	{
		return $this->getFormattedPictogramsArray($carrier);
	}

	/**
	 * 絵文字番号から、自身のキャリアの絵文字バイナリを返す。
	 * 数字以外が入ってきたときはすべてそのまま返す。
	 *
	 * @param integer $number
	 *
	 * @return string
	 */
	public function getPictogram($picNumber)
	{
		// セパレータでつながっている場合は、分割して複数返す。
		#TODO セパレータで 数字+文字というつなぎだった場合を考慮すると、_getPictogramの分割の仕方はおかしいかも？後で考慮
		$picBinary = '';
		$picUnpacked = '';
		if (strpos($picNumber, self::getPictogramSeparator())) {
			$picNumberList = explode(self::getPictogramSeparator(), $picNumber);
			foreach ($picNumberList as $number) {
				$picUnpacked .= $this->_getPictogram($number);
			}
			$picBinary = $this->toBinary($picUnpacked);
		} else if (is_numeric($picNumber)) {
			$picUnpacked = $this->_getPictogram($picNumber);
			$picBinary = $this->toBinary($picUnpacked);
		} else {
			$inputEncoding = mb_detect_encoding($picNumber, 'UTF-8, sjis-win, jis');
			if ($inputEncoding != $this->getCharacterEncoding()) {
				$picBinary = mb_convert_encoding($picNumber, $this->getCharacterEncoding(), $inputEncoding);
			} else {
				$picBinary = $picNumber;
			}
		}

		return $picBinary;
	}

	/**
	 * 絵文字番号から、絵文字のバイナリ文字列を返す。数字じゃなかったらそのまま返す。
	 *
	 * @param integer $number
	 *
	 * @return string $unpackedChar
	 */
	protected function _getPictogram($number)
	{
		if (array_key_exists($number, $this->pictograms[$this->getCarrier()])) {
			$return = $this->pictograms[$this->getCarrier()][$number];
		} else {
			$return = '';
		}

		return $return;
	}

	/**
	 * 絵文字のunpackされた値から絵文字番号を取得する
	 *
	 * @param string $unpackedChar
	 *
	 * @return integer
	 */
	protected function getPictogramNumber($unpackedChar)
	{
		return array_search($unpackedChar, $this->pictograms[$this->getCarrier()]);
	}

	/**
	 * 使用している文字エンコードを取得
	 *
	 * @return string
	 */
	public function getCharacterEncoding()
	{
		return $this->characterEncode;
	}

	/**
	 * エスケープシーケンスと、内部文字コードのPrefix/Suffixと同じ文字列をエスケープする
	 *
	 * @param string $inputString
	 *
	 * @return string
	 */
	public function escapeString($inputString)
	{
		$inputString = $this->_replaceString($this->getEscapeSequence(), $this->_escapeString($this->getEscapeSequence()), $inputString);    
		$inputString = $this->_replaceString($this->getIntercodePrefix(), $this->_escapeString($this->getIntercodePrefix()), $inputString);
		$inputString = $this->_replaceString($this->getIntercodeSuffix(), $this->_escapeString($this->getIntercodeSuffix()), $inputString);   
		$return = $inputString;

		return $return;
	}

	/**
	 * マルチバイト文字列をリプレースする。
	 *
	 * @param string $inputString
	 *
	 * @return string
	 */
	protected function _replaceString($search, $replace, $inputString)
	{
		if (mb_strpos($inputString, $search, 0, $this->getCharacterEncoding()) !== false) {
			#TODO:
			mb_regex_encoding($this->getCharacterEncoding());
			$return = mb_ereg_replace(preg_quote($search, '/'), $replace, $inputString, $this->getCharacterEncoding());
		} else {
			$return = $inputString;
		}
		return $return;
	}

	/**
	 * 渡された文字列をエスケープシーケンスを使ってエスケープする
	 *
	 * @param string $inputString
	 *
	 * @return string
	 */
	protected function _escapeString($inputString)
	{
		if (strlen($inputString) === 1) {
			$return = $this->getEscapeSequence() . $inputString;
		} else if (strlen($inputString) > 1) {
			$splitStringArray = str_split($inputString);
			$return = $this->getEscapeSequence() . implode($this->getEscapeSequence(), $splitStringArray);
		} else {
			$return = $inputString;
		}

		return $return;
	}

	/**
	 * エスケープを元に戻す
	 *
	 * @param string $inputString
	 *
	 * @return string
	 */
	public function unescapeString($inputString)
	{
		$inputString = $this->_replaceString($this->_escapeString($this->getIntercodePrefix()), $this->getIntercodePrefix(), $inputString);
		$inputString = $this->_replaceString($this->_escapeString($this->getIntercodeSuffix()), $this->getIntercodeSuffix(), $inputString);
		$inputString = $this->_replaceString($this->_escapeString($this->getEscapeSequence()), $this->getEscapeSequence(), $inputString);
		$return = $inputString;

		return $return;
	}

	/**
	 * 絵文字データをデータベースからロードする。
	 *
	 * @param string $carrier
	 */
	protected function loadPictograms($carrier)
	{
		if (isset($this->pictograms[$carrier]) && !empty($this->pictograms[$carrier])) return;

		$filename = $this->getPicdbDir() . '/' . $carrier . '_emoji.json';
		if (!file_exists($filename)) {
			throw new Text_Pictogram_Mobile_Exception("pictograms file ($filename) does not exist!");
		}

		$json = file_get_contents($filename);
		$pictograms = json_decode($json, true);
		foreach ($pictograms[$carrier] as $data) {
			$this->pictograms[$carrier][$data['number']] = $data[$this->getPictogramType()];
		}
	}

	/**
	 * 変換データをデータベースからロードする。
	 *
	 * @param string $carrier
	 */
	protected function loadConvertDB($carrier)
	{
		if (isset($this->convertDatabase[$carrier]) && !empty($this->convertDatabase[$carrier])) return;

		$filename = $this->getPicdbDir() . '/' . $carrier . '_convert.json';
		if (!file_exists($filename)) {
			throw new Text_Pictogram_Mobile_Exception("convert file ($filename) does not exist!");
		}

		$json = file_get_contents($filename);
		$convert = json_decode($json, true);
		$this->convertDatabase[$carrier] = $convert[$carrier];
	}


	/**
	 * 内部絵文字から絵文字を返す。
	 * ただし、入力文字はprefixとsuffixをとりぞのいたもの。。。にしてるけど、どうだろ。
	 * 
	 * @param string $intercode
	 *
	 * @return string
	 */
	protected function getPictogramIntercode($intercode)
	{
		list($carrierCode, $sourcePicNumber) = explode(' ', trim($intercode));

		#TODO: 今のままではcarrierCodeが絵文字DB依存。ここを疎にしたい。。。意味は無いかも。
		$sourceCarrier = $carrierCode;
		if (strtolower($sourceCarrier) != strtolower($this->getCarrier())) {
			$this->loadConvertDB($sourceCarrier);
			$picNumber = $this->convertDatabase[$sourceCarrier][$sourcePicNumber][$this->getCarrier()];
		} else {
			$picNumber = $sourcePicNumber;
		}

		return $this->getPictogram($picNumber);
	}

	/**
	 * 絵文字のバイナリ文字列を内部絵文字へ変換
	 *
	 * @param string $unpackedChar
	 *
	 * @return string
	 */
	protected function toIntercodeUnpacked($unpackedChar)
	{
		if ($this->isPictogramUnpacked($unpackedChar) === false) {
			$return = pack('H*', $unpackedChar);
		} else {
			$return = $this->getIntercodePrefix() . " " . $this->getCarrier() . " " . $this->getPictogramNumber($unpackedChar) . " " .  $this->getIntercodeSuffix();
		}

		return $return;
	}

	/**
	 * 絵文字バイナリを内部絵文字へ変換
	 *
	 * @param string $char
	 *
	 * @return string
	 */
	public function toIntercode($char)
	{
		return $this->toIntercodeUnpacked(strtoupper(bin2hex($char)));
	}

	/**
	 * unpack済みのバイナリが絵文字に相当するかどうかを判定する
	 *
	 * @param string $unpackedChar
	 * @param string $carrier
	 *
	 * @return boolian
	 */
	protected function isPictogramUnpacked($unpackedChar, $carrier = null)
	{
		if (is_null($carrier)) {
			$carrier = $this->getCarrier();
		}
		return in_array($unpackedChar, $this->pictograms[$carrier]);
	}

	/**
	 * 絵文字かどうかを判定する
	 * 
	 * @param string $char
	 *
	 * @return boolian
	 */
	public function isPictogram($char)
	{
		return $this->isPictogramUnpacked(strtoupper(bin2hex($char)));
	}

	/**
	 * 絵文字入り文字列を指定された方法で変換する
	 * 
	 * @param string $inputString 
	 * @param string $replaceMethod 
	 *
	 * @return string
	 */
	protected function _convert($inputString, $replaceMethod = 'toIntercodeUnpacked')
	{
		if (!strlen($inputString)) return $inputString;
		if (!strlen($replaceMethod)) return $replaceMethod;

		$hexString = strtoupper(bin2hex($inputString));
		$binaryArray = new TPM_Iterator_Agregate(str_split($hexString, 2), 0, 'TPM_Iterator_' . $this->encoding);
		$binaryArray->setPictograms($this->pictograms);

		$diff = 0;
		foreach ($binaryArray as $position => $picBinary)
		{
			$replace = $this->$replaceMethod($picBinary);
			$hexString = substr_replace($hexString, $replace, $position + $diff, strlen($picBinary));
			$diff += strlen($replace) - strlen($picBinary);
		}

		return pack('H*', $hexString);
	}


	/**
	 * 文字列全体を解析し、絵文字を内部絵文字に置換した文字列を返す
	 *
	 * @param string $inputString
	 *
	 * @return string
	 */
	public function convert($inputString)
	{
		return $this->_convert($this->escapeString($inputString));
	}

	/**
	 * 文字列を解析し、絵文字を現在のキャリア用に置換した文字列を返す
	 *
	 * @param string $inputString
	 * 
	 * @return string
	 */
	public function replace($inputString)
	{
		return $this->_convert($inputString, '_replace');
	}

	/**
	 * 絵文字かどうかを判定して、そうだったらキャリアを割り出して切り替える。
	 *
	 * @param string $checkString
	 * 
	 * @return string
	 */
	protected function _replace($checkString)
	{
		#TODO: キャリア名リテラルなんとかする。
		$carriers = array('docomo', 'ezweb', 'softbank');

		$return = null;
		foreach ($carriers as $carrier) {
			$this->loadPictograms($carrier);
			if (!$this->isPictogramUnpacked($checkString, $carrier)) continue;
			if ($this->getCarrier() == $carrier) {
				$return = $checkString;
				break;
			}
			if (!($sourceNumber = array_search($checkString, $this->pictograms[$carrier]))) continue;

			$this->loadConvertDB($carrier);
			$replace = $this->convertDatabase[$carrier][$sourceNumber][$this->getCarrier()];

			#TODO
			$return = $this->pictograms[$carrier][$replace];
			//$return = $this->getPictogram($replace);
			break;
		}

		if (is_null($return)) {
			$return = $checkString;
		}

		return $return;
	}

	/**
	 * 絵文字を削除する
	 *
	 * @param string $inputString
	 *
	 * @return string
	 */
	public function erase($inputString)
	{
		return $this->_convert($inputString, '_erase');
	}

	protected function _erase($inputString)
	{
		return '';
	}

	/**
	 * 内部絵文字を含む文字列を渡し、内部絵文字を各機種の絵文字に置換した文字列を返す
	 *
	 * @param string $inputString
	 *
	 * @return string
	 */
	public function restore($inputString)
	{
		if (!strlen($inputString)) return $inputString;

		return $this->unescapeString($this->_restore($inputString));
	}

	protected function _restore($inputString)
	{
		// 内部絵文字に該当する部分だけを抽出
		$pattern = '/' . preg_quote($this->getIntercodePrefix(), '/') . '(.*)' . preg_quote($this->getIntercodeSuffix(), '/') . '/mUs';
		preg_match_all($pattern, $inputString, $matches, PREG_SET_ORDER);

		$replaceArray = array();
		foreach ($matches as $match) {
			$replaceArray[$match[0]] = $this->getPictogramIntercode(trim($match[1]));
		}

		if (count($replaceArray)) {
			$restoreString = strtr($inputString, $replaceArray);
		} else {
			$restoreString = $inputString;
		}

		return $restoreString;
	}


////////////////////////////////////////////////////////
// accessor
////////////////////////////////////////////////////////

	/**
	 * キャリア名を返す
	 *
	 * @return string
	 */
	public function getCarrier()
	{
		return $this->carrier;
	}
	
	/**
	 * 返却する絵文字のタイプを指定する。
	 *
	 * @param string $type
	 */
	public function setPictogramType($type)
	{
		$this->pictogramType = $type;
	}

	/**
	 * 返却する絵文字のタイプを返す。
	 *
	 * @return string
	 */
	public function getPictogramType()
	{
		return $this->pictogramType;
	}

	/**
	 * 内部絵文字に使用するPrefixを設定する。
	 *
	 * @param string $prefix
	 */
	public function setIntercodePrefix($prefix)
	{
		$this->intercodePrefix = $prefix;
	}

	/**
	 * 内部絵文字に使用するPrefixを取得する。
	 *
	 * @return string
	 */
	public function getIntercodePrefix()
	{
		return $this->intercodePrefix;
	}

	/**
	 * 内部絵文字に使用するSuffixを設定する。
	 *
	 * @param string $suffix
	 */
	public function setIntercodeSuffix($suffix)
	{
		$this->intercodeSuffix = $suffix;
	}

	/**
	 * 内部絵文字に使用するSuffixを取得する。
	 *
	 * @return string
	 */
	public function getIntercodeSuffix()
	{
		return $this->intercodeSuffix;
	}

	/**
	 * 内部絵文字のPrefix, Suffixを設定する。
	 *
	 * @param string $prefix
	 * @param string $suffix
	 */
	public function setIntercode($prefix, $suffix)
	{
		$this->setIntercodePrefix($prefix);
		$this->setIntercodeSuffix($suffix);
	}

  /**
   * エスケープシーケンスを設定
   */
  public function setEscapeSequence($escapeSequence)
  {
    $this->escapeSequence = $escapeSequence;
  }

	/**
	 * エスケープシーケンスを取得
	 */
	public function getEscapeSequence()
	{
		return $this->escapeSequence;
	}

	/**
	 * 複数絵文字組み合わせの場合のセパレータを返す
	 * あんまり意味ない気がするけど、とりあえず。
	 *
	 * @return string
	 */
	public static function getPictogramSeparator()
	{
		return self::$pictogramSeparator;
	}

	/**
	 * 絵文字データベースのパスを取得
	 *
	 * @return string $picdbDir
	 */
	public function getPicdbDir()
	{
		return $this->picdbDir;
	}

	/**
	 * 絵文字データベースのパスを設定
	 *
	 * @param string $dir
	 */
	public function setPicdbDir($dir)
	{
		if (!(file_exists($dir) && is_dir($dir) && $realpath = realpath($dir))) {
			throw new Text_Pictogram_Mobile_Exception("setting pictogram DB path ($dir)does not exist!");
		}

		$this->picdbDir = $realpath;
	}
}
