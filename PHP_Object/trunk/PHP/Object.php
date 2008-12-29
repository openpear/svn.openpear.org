<?php
require_once 'PHP/Object/Array.php';
require_once 'PHP/Object/Boolean.php';
require_once 'PHP/Object/Null.php';
require_once 'PHP/Object/Numeric/Integer.php';
require_once 'PHP/Object/Numeric/Float.php';
require_once 'PHP/Object/Object.php';
require_once 'PHP/Object/Resource.php';
require_once 'PHP/Object/String.php';


class PHP_Object 
{
    protected $data = null;

    protected $aliasMethods = array();

    protected $methodsReturningSelf = array(
        'clearstatcache',
        'closedir',
        'date_date_set',
        'date_isodate_set',
        'date_modify',
        'date_time_set',
        'date_timezone_set',
        'dba_close',
        'debug_print_backtrace',
        'debug_zval_dump',
        'define_syslog_variables',
        'flush',
        'header',
        'ini_restore',
        'libxml_clear_errors',
        'libxml_set_streams_context',
        'mt_srand',
        'mysqli_free_result',
        'mysqli_set_local_infile_default',
        'mysqli_stmt_data_seek',
        'mysqli_stmt_free_result',
        'ncurses_bkgdset',
        'ncurses_filter',
        'ncurses_getmaxyx',
        'ncurses_getyx',
        'ncurses_init',
        'ncurses_noqiflush',
        'ncurses_qiflush',
        'ncurses_timeout',
        'ncurses_update_panels',
        'ncurses_use_env',
        'ob_clean',
        'ob_flush',
        'ob_implicit_flush',
        'openssl_free_key',
        'openssl_pkey_free',
        'openssl_x','_free',
        'parse_str',
        'passthru',
        'pcntl_exec',
        'register_shutdown_function',
        'restore_include_path',
        'rewinddir',
        'session_set_cookie_params',
        'session_unset',
        'session_write_close',
        'set_time_limit',
        'shmop_close',
        'socket_clear_error',
        'socket_close',
        'spl_autoload',
        'spl_autoload_call',
        'srand',
        'stream_bucket_append',
        'stream_bucket_prepend',
        'unregister_tick_function',
        'usleep',
        'var_dump',
        'zip_close',
    );

    protected $argOffsets = array();

    protected function __construct($data)
    {
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
            if (($offset = $this->argOffset($name)) !== null) {
                array_splice($args, $offset, 0, array(&$this->data));
            }
            $result = call_user_func_array($name, $args);

            if (in_array($name, $this->methodsReturningSelf)) {
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
            return new PHP_Object_String(&$data);
        } else if (is_numeric($data)) {
            if (is_int($data)) {
                return new PHP_Object_Numeric_Integer(&$data);
            } else if (is_float($data)) {
                return new PHP_Object_Numeric_Float(&$data);
            }
        } else if (is_array($data)) {
            return new PHP_Object_Array(&$data);
        } else if (is_bool($data)) {
            return new PHP_Object_Boolean(&$data);
        } else if (is_null($data)) {
            return new PHP_Object_Null(&$data);
        } else if (is_object($data)) {
            if ($data instanceof self) {
                return $data;
            } else {
                return new PHP_Object_Object(&$data);
            }
        } else if (is_resource($data)) {
            return new PHP_Object_Resource(&$data);
        }
    }

    protected function aliasMethod($name) {
        if (!function_exists($name)) {
            $aliases = str_replace('*', $name, $this->aliasMethods);
            foreach ($aliases as $alias) {
                if (function_exists($alias)) {
                    return $alias;
                }
            }
        }
        return $name;
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

    public function argOffset($name) 
    {
        return array_key_exists($name, $this->argOffsets) ? $this->argOffsets[$name] : null;
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
            if (($offset = $this->argOffset($name)) !== null) {
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
