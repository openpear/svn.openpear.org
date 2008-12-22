<?php
require_once 'PHP/Object/Array.php';
require_once 'PHP/Object/Boolean.php';
require_once 'PHP/Object/Null.php';
require_once 'PHP/Object/Numeric.php';
require_once 'PHP/Object/Numeric/Integer.php';
require_once 'PHP/Object/Object.php';
require_once 'PHP/Object/Resource.php';
require_once 'PHP/Object/String.php';


class PHP_Object 
{
    protected $data = NULL;

    protected $aliasMethods = array();

    protected $methodsReturningSelf = array(
        'debug_print_backtrace',
        'flush',
        'ini_restore',
        'ob_clean',
        'ob_flush',
        'ob_implicit_flush',
        'date_add',
        'date_date_set',
        'date_isodate_set',
        'date_modify',
        'register_shutdown_function',
        'restore_include_path',
        'session_set_cookie_params',
        'session_unset',
        'session_write_close',
        'set_time_limit',
        'usleep',
        'var_dump',
    );

    public $argOffsets = array(
        'array_fill_keys' => 1,
        'array_fill' => 2,
        'array_key_exists' => 0,
        'array_keys' => 1,
        'array_map' => NULL,
        'array_pad' => 2, 
        'array_push' => 1, 
        'array_reverse' => NULL,
        'array_search' => 0,
        'array_splice' => 3,
        'array_unshift' => 1,
        'array_walk_recursive' => 2,
        'array_walk' => 2,
        'assert_options' => 1,
        'checkdate' => NULL,
        'clearstatcache' => NULL,
        'closedir' => NULL,
        'connection_aborted' => NULL,
        'connection_status' => NULL,
        'connection_timeout' => NULL,
        'count' => 0,
        'date_add' => NULL,
        'date_date_set' => NULL,
        'date_default_timezone_get' => NULL,
        'date_format' => 1,
        'date_isodate_set' => NULL,
        'date_modify' => NULL,
        'date_offset_get' => NULL,
        'date_sub' => NULL,
        'date_sun_info' => NULL,
        'date' => NULL,
        'debug_backtrace' => NULL,
        'debug_print_backtrace' => NULL,
        'debug_zval_dump' => NULL,
        'define' => 1,
        'error_get_last' => NULL,
        'error_reporting' => NULL,
        'explode' => 1,
        'file_put_contents' => 1,
        'flush' => NULL,
        'fprintf' => 2,
        'func_get_args' => NULL,
        'func_num_args' => NULL,
        'get_browser' => NULL,
        'get_class' => NULL,
        'get_current_user' => NULL,
        'get_declared_classes' => NULL,
        'get_declared_interfaces' => NULL,
        'get_defined_constants' => NULL,
        'get_defined_functions' => NULL,
        'get_defined_vars' => NULL,
        'get_html_translation_table' => NULL,
        'get_include_path' => NULL,
        'get_included_files' => NULL,
        'get_loaded_extensions' => NULL,
        'get_magic_quotes_gpc' => NULL,
        'get_magic_quotes_runtime' => NULL,
        'get_parent_class' => NULL,
        'get_required_files' => NULL,
        'getcwd' => NULL,
        'getdate' => NULL,
        'getlastmod' => NULL,
        'getmygid' => NULL,
        'getmyinode' => NULL,
        'getmypid' => NULL,
        'getmyuid' => NULL,
        'getrusage' => NULL,
        'gettimeofday' => NULL,
        'gmdate' => NULL,
        'gmmktime' => NULL,
        'gmstrftime' => NULL,
        'ignore_user_abort' => NULL,
        'in_array' => 0,
        'ini_alter' => 1,
        'ini_get_all' => NULL,
        'ini_set' => 1,
        'lcg_value' => NULL,
        'localeconv' => NULL,
        'localtime' => NULL,
        'mb_check_encoding' => NULL,
        'mb_detect_order' => NULL,
        'mb_ereg_match' => 1,
        'mb_ereg_replace' => 2,
        'mb_ereg_search_getpos' => NULL,
        'mb_ereg_search_getregs' => NULL,
        'mb_ereg_search_pos' => NULL,
        'mb_ereg_search_regs' => NULL,
        'mb_ereg_search' => NULL,
        'mb_ereg' => 1,
        'mb_eregi_replace' => 2,
        'mb_eregi' => 1,
        'mb_get_info' => NULL,
        'mb_http_input' => NULL,
        'mb_http_output' => NULL,
        'mb_internal_encoding' => NULL,
        'mb_language' => NULL,
        'mb_list_encodings' => NULL,
        'mb_regex_encoding' => NULL,
        'mb_regex_set_options' => NULL,
        'mb_split' => NULL,
        'mb_substitute_character' => NULL,
        'memory_get_peak_usage' => NULL,
        'memory_get_usage' => NULL,
        'microtime' => NULL,
        'mktime' => NULL,
        'mt_getrandmax' => NULL,
        'mt_rand' => NULL,
        'mt_srand' => NULL,
        'mysql_affected_rows' => NULL,
        'mysql_client_encoding' => NULL,
        'mysql_close' => NULL,
        'mysql_connect' => NULL,
        'mysql_db_name' => 2,
        'mysql_errno' => NULL,
        'mysql_error' => NULL,
        'mysql_fetch_assoc' => NULLL,
        'mysql_get_client_info' => NULL,
        'mysql_get_host_info' => NULL,
        'mysql_get_proto_info' => NULL,
        'mysql_get_server_info' => NULL,
        'mysql_info' => NULL,
        'mysql_insert_id' => NULL,
        'mysql_list_dbs' => NULL,
        'mysql_list_processes' => NULL,
        'mysql_pconnect' => NULL,
        'mysql_ping' => NULL,
        'mysql_result' => 2,
        'mysql_stat' => NULL,
        'mysql_thread_id' => NULL,
        'mysql_unbuffered_query' => NULL,
        'ob_clean' => NULL,
        'ob_end_clean' => NULL,
        'ob_end_flush' => NULL,
        'ob_flush' => NULL,
        'ob_get_clean' => NULL,
        'ob_get_contents' => NULL,
        'ob_get_flush' => NULL,
        'ob_get_length' => NULL,
        'ob_get_level' => NULL,
        'ob_get_status' => NULL,
        'ob_implicit_flush' => NULL,
        'ob_list_handlers' => NULL,
        'ob_start' => NULL,
        'output_add_rewrite_var' => 1,
        'output_reset_rewrite_vars' => NULL,
        'php_ini_loaded_file' => NULL,
        'php_ini_scanned_files' => NULL,
        'php_logo_guid' => NULL,
        'php_sapi_name' => NULL,
        'php_uname' => NULL,
        'phpcredits' => NULL,
        'phpinfo' => NULL,
        'phpversion' => NULL,
        'pi' => NULL,
        'preg_grep' => 1,
        'preg_last_error' => NULL,
        'preg_match' => 1,
        'preg_match_all' => 1,
        'preg_replace_callback' => 2,
        'preg_replace' => 2,
        'preg_split' => 1,
        'printf' => 1,
        'rand' => NULL,
        'readdir' => NULL,
        'restore_error_handler' => NULL,
        'restore_exception_handler' => NULL,
        'restore_include_path' => NULL,
        'rewinddir' => NULL,
        'session_cache_expire' => NULL,
        'session_cache_limiter' => NULL,
        'session_commit' => NULL,
        'session_destroy' => NULL,
        'session_encode' => NULL,
        'session_get_cookie_params' => NULL,
        'session_id' => NULL,
        'session_module_name' => NULL,
        'session_name' => NULL,
        'session_regenerate_id' => NULL,
        'session_save_path' => NULL,
        'session_set_save_handler' => NULL,
        'session_start' => NULL,
        'session_unset' => NULL,
        'session_write_close' => NULL,
        'sprintf' => 1,
        'srand' => NULL,
        'str_ireplace' => 2,
        'str_replace' => 2,
        'sys_get_temp_dir' => NULL,
        'sys_getloadavg' => NULL,
        'strchr' => 1,
        'strstr' => 1,
        'strrchr' => 1,
        'time' => NULL,
        'timezone_abbreviations_list' => NULL,
        'timezone_identifiers_list' => NULL,
        'timezone_transitions_get' => NULL,
        'tmpfile' => NULL,
        'umask' => NULL,
        'uniqid' => NULL,
        'zend_logo_guid' => NULL,
        'zend_thread_id' => NULL,
        'zend_version' => NULL,
    );


    public function __construct($data = NULL)
    {
        $this->data = $data;

        $parent = get_parent_class($this);
        while ($parent !== false) {
            $vars = get_class_vars($parent);
            $this->argOffsets = array_merge($vars['argOffsets'], $this->argOffsets);
            $parent = get_parent_class($parent);
        }
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
            if (($offset = $this->argOffset($name)) !== NULL) {
                array_splice($args, $offset, 0, array(&$this->data));
            }
            $result = call_user_func_array($name, $args);

            if (in_array($name, $this->methodsReturningSelf)) {
                return $this;
            } else {
                return self::factory($result);
            }
        }

        if (($result = $this->callConstructAsMethod($name, $args)) != false) {
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


    public static function factory($data = NULL)
    {
        if (is_string($data)) {
            return new PHP_Object_String($data);
        } else if (is_numeric($data)) {
            if (is_int($data)) {
                return new PHP_Object_Numeric_Integer($data);
            } else {
                return new PHP_Object_Numeric($data);
            }
        } else if (is_array($data)) {
            return new PHP_Object_Array($data);
        } else if (is_bool($data)) {
            return new PHP_Object_Boolean($data);
        } else if (is_null($data)) {
            return new PHP_Object_Null($data);
        } else if (is_object($data)) {
            if ($data instanceof self) {
                return $data;
            } else {
                return new PHP_Object_Object($data);
            }
        } else if (is_resource($data)) {
            return new PHP_Object_Resource($data);
        } else {
            return new PHP_Object($data);
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

    protected function callConstructAsMethod($name, $args)
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
        return array_key_exists($name, $this->argOffsets) ? $this->argOffsets[$name] : 0;
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
            if (($offset = $this->argOffset($name)) !== NULL) {
                array_splice($args, $offset, 0, array(&$this->data));
            }
            call_user_func_array($name, $args);
            return $this;
        }

        if (($result = $this->callConstructAsMethod($name, $args)) != false) {
            return $this;
        }

    }

}
