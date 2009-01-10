<?php

require_once 'Text/Pictogram/Mobile/Common.php';

class Text_Pictogram_Mobile_Ezweb extends Text_Pictogram_Mobile_Common
{
	public function __construct($type = 'sjis')
	{
		$this->carrier = 'ezweb';
		$this->setPictogramType($type);
		switch ($type) {
			case 'sjis':
				$encode = 'sjis-win';
				break;
			case 'utf-8':
				$encode = 'UTF-8';
				break;
			case 'jis-email':
				$encode = 'ISO-2022-JP';
				break;
			default:
				$encode = 'sjis-win';
				break;
		}

		$this->characterEncode = $encode;

		$this->initialize();
	}

	public function toBinary($unpackedChar)
	{
		switch ($this->getPictogramType()) {
			case "jis-email":
				$unpackedChar = '1B2442' . $unpackedChar . '1B2842';
				break;
			default:
				break;
		}
		$result = pack('H*', $unpackedChar);

		return $result;
	}

}
