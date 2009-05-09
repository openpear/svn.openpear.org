<?php
/**
 * Mail_mime_XPath.php
 *
 * @author Daichi Kamemoto(a.k.a: yudoufu) <daikame@gmail.com>
 */
/**
 * The MIT License
 *
 * Copyright (c) 2008 Daichi Kamemoto <daikame@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

require_once 'Mail/mimeDecode.php';


/**
 * メールデータをパースし、XPath式を使って該当データを取得する
 *
 * @author Daichi Kamemoto
 * @version 0.0.1
 */
class Mail_mime_XPath
{
	private $structure;
	private $xml;

	// プロパティに追加するデータ
	private static $attr = array(
										'content-type',
										'filename',
										'ctype_primary',
										'ctype_secondary',
									);

	/**
	 * コンストラクタ
	 */
	public function __construct($input)
	{
		$params["include_bodies"] = true;
		$params["decode_bodies"] = true;
		$params["decode_headers"] = true;
		$params["input"] = $input;
		$params["crlf"] = "\r\n";

		$this->structure = Mail_mimeDecode::decode($params);
		
		$this->arrayToXML();
	}

	/**
	 * パースしたメールデータからXPath式に従ってデータを配列で取得
	 */
	public function xpath($path)
	{
		$obj = $this->xml->xpath($path);

		$ret = null;
		$this->rebuild($obj, $ret);

		return $ret;
	}

	/**
	 * Mail_mimeDecodeの配列を内部処理用にXMLに直す
	 */
	private function arrayToXML()
	{
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><rootDocument />');

		$this->_arrayToXML($this->structure, $xml);

		$this->xml = $xml;
	}

	/**
	 * 再帰的にXMLに直していく
	 */
	private function _arrayToXML($input, $parent)
	{
		if (is_object($input))
		{
			$this->part = $parent;
		}

		foreach ($input as $key => $value)
		{
			if (is_numeric($key))
			{
				$key = 'numberNode_' . $key;
			}

			if (is_array($value) || is_object($value))
			{
				$node = $parent->addChild($key);
				$this->_arrayToXML($value, $node);
			}
			else
			{
				$parent->addChild($key, base64_encode($value));

				// メインのオブジェクトにプロパティ追加
				if (in_array($key, self::$attr))
				{
					$tmp = explode(';', $value);
					$value = array_shift($tmp);
					$this->part->addAttribute($key, $value);
				}
			}
		}
	}

	/**
	 * データをデコードしつつ配列に入れていく
	 */
	private function rebuild($data, &$ret)
	{
		foreach ($data as $key => $value)
		{
			$ret[$key]= base64_decode($value[0]);
			$this->rebuild($value, $ret[$key]);
		}
	}
}


