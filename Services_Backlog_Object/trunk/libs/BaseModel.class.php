<?php
/**
 * model class base
 */
abstract class BaseModel
{
    protected $fields = array();
    protected $values = array();

    protected $backlog;

    /**
     * constructor
     *
     * @param array $values initialize varues
     */
    public function __construct($values = null, $backlog = null)
    {
        $this->setDefalut($values);
        $this->setBacklog($backlog);
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
     * backlog setter
     *
     * @param Services_BacklogObject $backlog
     * @return null
     */
    public function setBacklog($backlog)
    {
        $this->backlog = $backlog;
    }

    /**
     * backlog getter
     *
     * @return Services_BacklogObject
     */
    public function getBacklog()
    {
        return $this->backlog;
    }

    /**
     * backlog has?
     *
     * @return boolean
     */
    public function hasBacklog()
    {
        return $this->backlog instanceof Services_BacklogObject;
    }

    /**
     *
     * @param unknown_type $name
     * @param unknown_type $arguments
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'set') {
            return $this->set($this->toSnake(substr($name, 3)), $arguments);
        } else if (substr($name, 0, 3) == 'get') {
            return $this->get($this->toSnake(substr($name, 3)));
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
        trigger_error(sprintf('undefined property: %s in %s on line %d', $name, $trace[0]['file'], $trace[0]['line']));
        return false;
    }

    /**
     * toPascal
     *
     * @param string $string
     * @return string
     */
    private function toPascal($string)
    {
        $string = strtolower($string);
        $string = str_replace('_', ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        return $string;
    }

    /**
     * toSnake
     * @param string $string
     * @return string
     */
    private function toSnake($string)
    {
        $string = preg_replace('/([A-Z])/', '_$1', $string);
        $string = strtolower($string);
        return ltrim($string, '_');
    }
}
