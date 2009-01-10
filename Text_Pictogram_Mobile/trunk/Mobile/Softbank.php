<?php

require_once 'Text/Pictogram/Mobile/Common.php';

class Text_Pictgram_Mobile_Softbank extends Text_Pictgram_Mobile_Common
{
	public function __construct($type = 'sjis')
	{
		$this->carrier = 'softbank';
		$this->setPictogramType($type);

		switch ($type) {
			case 'sjis':
				$encode = 'sjis-win';
				break;
			case 'utf-8':
				$encode = 'UTF-8';
				break;
			default:
				$encode = 'sjis-win';
				break;
		}

		$this->characterEncode = $encode;

		$this->initialize();
	}

	protected function toBinary($unpackedChar)
	{
		return pack('H*', $unpackedChar);
	}
}
