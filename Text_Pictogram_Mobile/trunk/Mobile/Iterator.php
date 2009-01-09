<?php

class TPM_Iterator extends ArrayIterator
{
	protected $current = '';
	protected $key = 0;
	protected $carrier = '';

	protected $pictograms;

	/**
	 * コンストラクタ
	 */
	public function __construct($pictograms)
	{
		$this->pictograms = $pictograms;
	}

	/**
	 * 現在の位置の絵文字HEXを取得
	 *
	 * @return string;
	 */
	public function current()
	{
		return $this->current;
	}

	/**
	 * 現在のバイナリ位置を取得
	 *
	 * @return number
	 */
	public function key()
	{
		return $this->key;
	}

	/**
	 * キャリアを設定
	 *
	 * @param string $carreir
	 */
	public function setCarrier($carrier)
	{
		$this->carreir = $carrier;
	}

	/**
	 * 現在の絵文字のキャリア名を取得
	 *
	 * @return string
	 */
	public function getCarreir()
	{
		return $this->carreir;
	}

	/**
	 * 文字列が絵文字かどうかを絵文字DBを元に判定
	 * キャリア名を与えた場合には、そのキャリアの絵文字かどうかのみチェック
	 *
	 * @param string $string
	 * @param string $carrier
	 * @return boolian
	 */
	protected function isPictogram($string, $carrier = '')
	{
		$return = false;
		if ($this->carrier = $carreir)
		{
			if (!empty($this->pictograms[$carrier])) 
			{
				$return = in_array($string, $this->pictograms[$carrier]);
			}
		}
		else
		{
			foreach ($this->pictograms as $ca => $data)
			{
				if ($return = in_array($string, $data) && $this->carrier = $ca) break;
			}
		}

		return $return;
	}
}

class TPM_Iterator_Agregate extends ArrayObject
{
	private $pictograms;

	public function setPictograms($pictograms)
	{
		$this->pictograms = $pictograms;
	}

	public function getIterator()
	{
		$it_class = $this->getIteratorClass();
		return new $it_class($this->pictograms);
	}
}

class TPM_Iterator_SJIS extends TPM_Iterator
{
	public function next()
	{
		while (parent::valid())
		{
			$key = parent::key();

			$checkString = parent::current();
			parent::next();
			if (!parent::valid()) continue;

			// single byte check
			$hex = '0x' . $checkString;
			if (($hex >= 0x00 && $hex <= 0x7F) || ($hex >= 0xA1 && $hex <= 0xDF)) continue;

			$checkString .= parent::current();
			parent::next();

			if ($this->isPictogram($checkString))
			{
				$this->current = $checkString;
				$this->key = $key;
				break;
			}

			#TODO: この判定が必要か再考。必要だったらparent::next()の位置を変更する。
			#if (!$this->isMultibyte($checkString)) continue;
		}

		return;
	}

	protected function isMultibyte($string)
	{
		$bytes = str_split($string, 2);
		$bytes[0] = '0x' . $bytes[0];
		$bytes[1] = '0x' . $bytes[1];
		$result = false;

		// 本当は、SJISの定義そのものは0xEFまで。
		if (($bytes[0] >= 0x81 && $bytes[0] <= 0x9F) || ($bytes[0] >= 0xE0 && $bytes[0] <= 0xFC))
		{
			if (($bytes[1] >= 0x40 && $bytes[1] <= 0x7E) || ($bytes[1] >= 0x80 && $bytes[1] <= 0xFC))
			{
				$result = true;
			}
		}

		return $result;
	}
}


class TPM_Iterator_UTF8 extends TPM_Iterator
{
	public function next()
	{
		static $picAreaFlags = array('EE', 'EF');

		while (parent::valid())
		{
			$key = parent::key();

			$checkString = parent::current();
			parent::next();
			if (!parent::valid()) continue;

			// Pictogram Flag Check
			if (!in_array($checkString, $picAreaFlags)) continue;

			$checkString .= parent::current();
			parent::next();
			if (!parent::valid()) continue;

			$checkString .= parent::current();
			parent::next();

			if ($this->isPictogram($checkString))
			{
				$this->current = $checkString;
				$this->key = $key;
				break;
			}
		}

		return;
	}
}

class TPM_Iterator_JIS extends TPM_Iterator
{
	private $mb_flag = false;

	public function next()
	{
		while (parent::valid())
		{
			if (!$this->mb_flag && !$this->_mb_start()) continue;
			while (!$this->_mb_end() && parent::valid())
			{
				$key = parent::key();

				$checkString = parent::current();
				parent::next();
				if (!parent::valid()) continue;
				$checkString .= parent::current();
				parent::next();

				if ($this->isPictogram($checkString))
				{
					$this->current = $checkString;
					$this->key = $key;
					break 2;
				}
			}
		}

		return;
	}

	private function _mb_start()
	{
		if (parent::current() !== '1B') return false;
		parent::next();
		if (!parent::valid() || parent::current() !== '24') return false;
		parent::next();
		if (!parent::valid() || parent::current() !== '42') return false;
		parent::next();

		return $this->mb_status = true;
	}

	private function _mb_end()
	{
		if (parent::current() !== '1B') return false;
		parent::next();
		if (!parent::valid() || parent::current() !== '28') return false;
		parent::next();
		if (!parent::valid() || parent::current() !== '42') return false;
		parent::next();

		$this->mb_status = false;
		return true;
	}
}
