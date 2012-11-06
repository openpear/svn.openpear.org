<?php
/**
 * model class base
 */
abstract class BaseModel
{
    protected $fields = array();
    protected $values = array();

    /**
     * constructor
     *
     * @param array $values initialize varues
     */
    public function __construct($values = null)
    {
        $this->setDefalut($values);
    }

    /**
     * initialize properties
     *
     * @param array $values
     */
    protected function setDefalut($values = null)
    {
        $this->values = array();
        foreach ($this->fields as $field) {
            $this->values[$field] = null;
        }

        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $this->set($key, $value);
            }
        }
    }

    /**
     *
     * @param unknown_type $name
     * @param unknown_type $arguments
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'set') {
            return $this->set(strtolower(substr($name, 3)), $arguments);
        } else if (substr($name, 0, 3) == 'get') {
            return $this->get(strtolower(substr($name, 3)));
        }
    }

    /**
     * setter properties
     *
     * @param string $name
     * @param string $value
     */
    protected function set($name, $value)
    {
        if ($this->hasProperty($name)) {
            $this->values[$name] = $value;
        }
    }

    /**
     * getter properties
     *
     * @param string $name
     * @return multitype:|NULL
     */
    protected function get($name)
    {
        if ($this->hasProperty($name)) {
            return $this->values[$name];
        }
        return null;
    }

    /**
     * has property?
     *
     * @param string $name
     * @return boolean
     */
    protected function hasProperty($name)
    {
        if (array_key_exists($name, $this->values)) {
            return true;
        }

        $trace = debug_backtrace();
        trigger_error(sprintf('undefined property: %s in %s on line %s', $name, $trace[0]['file'], $trace[0]['line']), E_NOTICE);
        return false;
    }
}
