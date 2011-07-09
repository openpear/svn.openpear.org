<?php

require_once 'PHPH/Util.php';

class PHPH_ReflectionFunction extends ReflectionFunction
{
	public function getNamespaceName()
	{
		$ns = null;
		if (method_exists("ReflectionFunction", "getNamespaceName")) {
			$ns_tmp = parent::getNamespaceName();
			if (0<strlen($ns_tmp)) {
				$ns = $ns_tmp;
			}
		}
		return $ns;
	}

	public function getShortName()
	{
		$name = null;
		if (method_exists("ReflectionFunction", "getShortName")) {
			$name = parent::getShortName();
		} else {
			$name = $this->getName();
		}
		return $name;
	}
}
