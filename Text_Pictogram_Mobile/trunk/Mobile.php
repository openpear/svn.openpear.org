<?php
/**
 * Text_Pictogram_Mobile.php
 *
 * @author Daichi Kamemoto <daikame@gmail.com>
 */
/**
 * The MIT License
 *
 * Copyright (c) 2007 - 2008 Daichi Kamemoto <daikame@gmail.com>
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

require_once 'Mobile/Exception.php';

/**
 * Text_Pictogram_Mobile - mobile絵文字を処理するクラス
 *
 * @package Text_Pictogram_Mobile
 * @author  Daichi Kamemoto <daikame@gmail.com>
 * @version 0.1.0 
 */
class Text_Pictogram_Mobile
{
	private static $agentRegexs = array(
										'docomo'   => '/^DoCoMo/',
										'ezweb'    => '/^(KDDI-|UP\.Browser)/',
										'softbank' => '/^(SoftBank|Vodafone|MOT-|J-PHONE)/',
										'willcom'  => '/^Mozilla\/3\.0\((?:DDIPOCKET|WILLCOM);/',
										'emobile'  => '/^emobile/',
									);

	/**
	 * Singleton を返す
	 *
	 * @param string $carrier   生成するオブジェクトのキャリア指定
	 * @return object
	 */
	public static function singleton($carrier = null)
	{
		static $instance;

		if (!isset($instance))
		{
			$instance = array();
		}

		if (is_null($carrier))
		{
			$carrier = self::getCarrier();
		}

		if (!isset($instance[$carrier]))
		{
			$instance[$carrier] = self::factory($carrier);
		}

		return $instance[$carrier];
	}

	/**
	 * UserAgentによるキャリア判別
	 *
	 * @return string
	 */
	private static function getCarrier()
	{
		$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$userCarrier = 'nonmobile';

		foreach (self::$agentRegexs as $carrier => $pattern)
		{
			if (preg_match($pattern, $userAgent))
			{
				$userCarrier = $carrier;
				break;
			}
		}

		return $carrier;
	}

	/**
	 * 絵文字操作オブジェクト生成
	 *
	 * @param string $carrier
	 * @return object
	 */
	public static function factory($carrier = null, $type = 'sjis')
	{
		if (isset($carrier) && $carrier != "") {
			switch (strtolower($carrier)) {
				case 'docomo':
				case 'imode':
				case 'i-mode':
					$carrier = 'docomo';
					break;
				case 'ezweb':
				case 'au':
				case 'kddi':
					$carrier = 'ezweb';
					break;
				case 'disney':
				case 'softbank':
				case 'vodafone':
				case 'jphone':
				case 'j-phone':
					$carrier = 'softbank';
					break;
				default:
					$carrier = 'nonmobile';
			}
			$carrier = ucfirst(strtolower($carrier));
		} else {
			$carrier = 'Nonmobile';
		}

		$className = "Text_Pictogram_Mobile_{$carrier}";
		if (!class_exists($className)) {
			$file = str_replace('_', '/', $className) . '.php';
			if (!include_once $file) {
				throw new Text_Pictogram_Mobile_Exception('Class file not found:' . $file);
			}
		}

    if (isset($type) && $type != '') {
      switch (strtolower($type)) {
        case 'sjis':
        case 'sjis-win':
        case 'shift_jis':
          $type = 'sjis';
          break;
        case 'utf-8':
        case 'utf8':
          $type = 'utf-8';
          break;
        case 'jis':
        case 'iso-2022-jp':
        case 'jis-email':
          $type = 'jis-email';
          break;
        default:
          $type = 'sjis';
          break;
      }
    }

		$instance = new $className($type);
		return $instance;
	}
}
