<?php
require_once 'PHP/Object/Array.php';
require_once 'PHP/Object/Boolean.php';
require_once 'PHP/Object/Configure.php';
require_once 'PHP/Object/Null.php';
require_once 'PHP/Object/Numeric/Integer.php';
require_once 'PHP/Object/Numeric/Float.php';
require_once 'PHP/Object/Object.php';
require_once 'PHP/Object/Resource.php';
require_once 'PHP/Object/String.php';


class PHP_Object 
{
    protected $data;

    protected $argOffsets = array();

    public $configure;

    protected function __construct(&$data)
    {
        $this->configure = PHP_Object_Configure::getInstance(get_class($this));
        $this->data = &$data;
    }

    protected function __toString() 
    {
        return (string)$this->data;
    }

    protected function __call($name, $args = array())
    {   
        $args = array_map(array($this, 'revert'), $args);
        $name = $this->aliasMethod($name);

        if (function_exists($name)) {
            if (($offset = $this->argOffset($name)->val()) !== null) {
                array_splice($args, $offset, 0, array(&$this->data));
            }
            $result = call_user_func_array($name, $args);

            if ($this->isMethodReturningSelf($name)->val()) {
                return $this;
            } else {
                return self::factory($result);
            }
        }

        if (($result = $this->callLanguageConstruct($name, $args)) != false) {
            return $result;
        }
    }

    protected function __get($name)
    {
        if (method_exists($this, $name)) {
            $method = new ReflectionMethod($this, $name);
            if ($method->isPublic()) {
                return $this->$name();
            }
        }
        return $this->__call($name);
    }


    public static function factory($data = null)
    {
        if (is_string($data)) {
            return new PHP_Object_String($data);
        } else if (is_numeric($data)) {
            if (is_int($data)) {
                return new PHP_Object_Numeric_Integer($data);
            } else if (is_float($data)) {
                return new PHP_Object_Numeric_Float($data);
            }
        } else if (is_array($data)) {
            return new PHP_Object_Array($data);
        } else if (is_bool($data)) {
            return PHP_Object_Boolean::getInstance($data);
        } else if (is_null($data)) {
            return PHP_Object_Null::getInstance();
        } else if (is_object($data)) {
            if ($data instanceof self) {
                return $data;
            } else {
                return new PHP_Object_Object($data);
            }
        } else if (is_resource($data)) {
            return new PHP_Object_Resource($data);
        }
    }

    protected function aliasMethod($method)
    {
        return $this->configure->aliasMethod($method);
    }

    protected function callLanguageConstruct($name, $args)
    {
        switch ($name) {
            case 'echo':
                array_unshift($args, $this->data);
                foreach ($args as $arg) {
                    echo $arg;
                }
                return $this;
            case 'print':
                print $this->data;
                return $this;
            case 'isset':
                array_unshift($args, $this->data);
                foreach ($args as $arg) {
                    if (!isset($arg)) {
                        return self::factory(false);
                    }
                }
                return self::factory(true);
            case 'unset':
                unset($this);
                return self::factory();
            case 'empty':
                return self::factory(empty($this->data));
            case 'eval':
                return self::factory(eval($this->data));
            case 'exit':
                exit($this->data);
            case 'die':
                die($this->data);
            case 'require':
                require $this->data;
                return $this;
            case 'include':
                include $this->data;
                return $this;
            case 'require_once':
                require_once $this->data;
                return $this;
            case 'include_once':
                include_once $this->data;
                return $this;
            default:
                return false;
        }
    }

    protected function &revert($data) 
    {
        if ($data instanceof self) {
            $data = $data->val();
        }
        return $data;
    }

    public function argOffset($method) 
    {
        return self::factory($this->configure->argOffset($method));
    }

    public function isMethodReturningSelf($method) {
        return self::factory($this->configure->isMethodReturningSelf($method));
    }

    public function val() 
    {
        return $this->data;
    }
    
    public function tap()
    {
        $args = func_get_args();
        $args = array_map(array($this, 'revert'), $args);
        $name = $this->aliasMethod(array_shift($args));

        if (function_exists($name)) {
            if (($offset = $this->argOffset($name)->val()) !== null) {
                array_splice($args, $offset, 0, array(&$this->data));
            }
            call_user_func_array($name, $args);
            return $this;
        }

        if (($result = $this->callLanguageConstruct($name, $args)) != false) {
            return $this;
        }

    }

}
