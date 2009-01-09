<?php
abstract class TPM_Data_Accessor implements ArrayAccess
{
	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	public function offsetExists($offset)
	{
		return $this->contains($offset);
	}

	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}

	public function __set($name, $value)
	{
		$this->set($name, $value);
	}

	public function __get($name)
	{
		return $this->get($name);
	}

	public function __isset($name)
	{
		return $this->contains($name);
	}

	public function __unset($name)
	{
		$this->remove($name);
	}

	public function __call($name, $arguments)
	{
		#TODO
		if (in_array($type = substr($name, 0, 3), array('get', 'set')))
		{
			$entityName = strtolower(substr($name, 3));
			$return =  $this->$type($entityName, $arguments);
		}
		else
		{
			throw new Text_Pictogram_Mobile_Exception("Method [$name] doesn't exist");
		}
	}
}

class TPM_Data extends TPM_Data_Accessor
{
	private $id;
	private $hex;

	public function __construct($id, $hex)
	{
		$this->id = $id;
		$this->hex = strtoupper($hex);
	}

	public function get($name)
	{
		return $this->_get($name);
	}

	public function set($name, $value)
	{
		$this->_set($name, $value);
	}

	public function contains($name)
	{
		return $this->_contains($name);
	}

	public function remove($name)
	{
		$this->_remove($name);
	}

	private function _get($name)
	{
		if (isset($this->data[$name]))
		{
			return $this->data[$name];
		}
		else
		{
			throw new Text_Pictogram_Mobile_Exception("entity [$name] doesn't exist");
		}
	}

	private function _set($name, $value)
	{
		$this->data[$name] = $value;
	}

	private function _contains($name)
	{
		return isset($this->data[$name]);
	}

	private function _remove($name)
	{
		#TODO:
		if ($this->_isset($name)) {
			unset($this->data[$name]);
		}
	}

	public function getBinary()
	{
		return pack('H*', $this->hex);
	}

	public function getUnpacked()
	{
		return $this->hex;
	}
}

interface TPM_DataStorage_Interface
{
	public function setDatabase();
	public function loadDatabase();
	public function getPictograms();
}

class TPM_DataStorage implements TPM_DataStorage_Interface
{
	private $pictograms;
	private $database;

	public function setDatabase()
	{
	}

	public function loadDatabase()
	{
		#TODO:
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

	public function loadConvert()
	{
		#TODO:
		if (isset($this->convertDatabase[$carrier]) && !empty($this->convertDatabase[$carrier])) return;

		$filename = $this->getPicdbDir() . '/' . $carrier . '_convert.json';
		if (!file_exists($filename)) {
			throw new Text_Pictogram_Mobile_Exception("convert file ($filename) does not exist!");
		}

		$json = file_get_contents($filename);
		$convert = json_decode($json, true);
		$this->convertDatabase[$carrier] = $convert[$carrier];
	}

	public function getPictograms()
	{
		return $this->pictograms;
	}
}
