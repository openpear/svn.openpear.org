<?php

require_once 'Text/Pictogram/Mobile/Common.php';

class Text_Pictogram_Mobile_Nonmobile extends Text_Pictogram_Mobile_Common
{

	public function __construct()
	{
		#TODO: sfの削除
		$this->getEscapeSequence(sfConfig::get('sf_pictogram_mobile_escape', '&'));
		$this->setIntercodePrefix(sfConfig::get('sf_pictogram_mobile_prefix', '[({'));
		$this->setIntercodeSuffix(sfConfig::get('sf_pictogram_mobile_suffix', '})]'));
	}

	public function getFormattedPictogramsArray($carrier = null)
	{
		return array();
	}

	public function convert($inputString)
	{
		return $inputString;
	}

	public function replace($inputString)
	{
		return $inputString;
	}

	public function restore($inputString)
	{
		$pattern = '/' . preg_quote($this->getIntercodePrefix(), '/') . '(.*)' . preg_quote($this->getIntercodeSuffix(), '/') . '/mUs';
		$restoreString = preg_replace($pattern, '', $inputString);

		return $restoreString;
	}
}
