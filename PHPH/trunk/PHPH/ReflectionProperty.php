<?php

class PHPH_ReflectionProperty extends ReflectionProperty
{
	public function getAccessFlag()
	{
		$flag = array();
		if ($this->isPublic()) {
			$flag[] = "ZEND_ACC_PUBLIC";
		}
		if ($this->isProtected()) {
			$flag[] = "ZEND_ACC_PROTECTED";
		}
		if ($this->isPrivate()) {
			$flag[] = "ZEND_ACC_PRIVATE";
		}
		if ($this->isStatic()) {
			$flag[] = "ZEND_ACC_STATIC";
			//$flag[] = "ZEND_ACC_ALLOW_STATIC";
		}
		if (count($flag)==0) {
			$flag[] = "0";
		}
		return implode(" | ", $flag);
	}
}
