<?php
require_once 'PHP/Object.php';

class PHP_Object_Array extends PHP_Object implements Iterator, ArrayAccess
{
    protected $aliasMethods = array(
        'array_*',
    );

    protected $argOffsets = array(
        'array_change_key_case' => 0,
        'array_chunk' => 0,
        'array_combine' => 0,
        'array_count_values' => 0,
        'array_diff' => 0,
        'array_diff_assoc' => 0,
        'array_diff_key' => 0,
        'array_diff_uassoc' => 0,
        'array_diff_ukey' => 0,
        'array_fill' => 2,
        'array_fill_keys' => 0,
        'array_filter' => 0,
        'array_flip' => 0,
        'array_intersect' => 0,
        'array_intersect_assoc' => 0,
        'array_intersect_key' => 0,
        'array_intersect_uassoc' => 0,
        'array_intersect_ukey' => 0,
        'array_key_exists' => 1,
        'array_keys' => 0,
        'array_map' => 1,
        'array_merge' => 0,
        'array_merge_recursive' => 0,
        'array_multisort' => 0,
        'array_pad' => 0,
        'array_pop' => 0,
        'array_product' => 0,
        'array_push' => 0,
        'array_rand' => 0,
        'array_reduce' => 0,
        'array_reverse' => 0,
        'array_search' => 1,
        'array_shift' => 0,
        'array_slice' => 0,
        'array_splice' => 0,
        'array_sum' => 0,
        'array_udiff' => 0,
        'array_udiff_assoc' => 0,
        'array_udiff_uassoc' => 0,
        'array_uintersect' => 0,
        'array_uintersect_assoc' => 0,
        'array_uintersect_uassoc' => 0,
        'array_unique' => 0,
        'array_unshift' => 0,
        'array_values' => 0,
        'array_walk' => 0,
        'array_walk_recursive' => 0,
        'arsort' => 0,
        'asort' => 0,
        'assert' => 0,
        'call_user_func' => 1,
        'call_user_func_array' => 1,
        'call_user_method' => 2,
        'call_user_method_array' => 2,
        'count' => 0,
        'compact' => 0,
        'current' => 0,
        'debug_zval_dump' => 0,
        'dns_get_mx' => 1,
        'each' => 0,
        'end' => 0,
        'ereg' => 2,
        'eregi' => 2,
        'exec' => 1,
        'extract' => 0,
        'file_put_contents' => 1,
        'filter_input_array' => 1,
        'filter_var' => 0,
        'filter_var_array' => 0,
        'fputcsv' => 1,
        'getimagesize' => 1,
        'getmxrr' => 1,
        'getopt' => 1,
        'gettype' => 0,
        'http_build_query' => 0,
        'iconv_mime_encode' => 2,
        'implode' => 1,
        'in_array' => 1,
        'intval' => 0,
        'is_array' => 0,
        'is_bool' => 0,
        'is_double' => 0,
        'is_float' => 0,
        'is_int' => 0,
        'is_integer' => 0,
        'is_long' => 0,
        'is_null' => 0,
        'is_numeric' => 0,
        'is_object' => 0,
        'is_real' => 0,
        'is_resource' => 0,
        'is_scalar' => 0,
        'is_string' => 0,
        'iterator_apply' => 2,
        'join' => 1,
        'json_encode' => 0,
        'key' => 0,
        'key_exists' => 1,
        'krsort' => 0,
        'ksort' => 0,
        'max' => 0,
        'mb_convert_encoding' => 2,
        'mb_convert_variables' => 2,
        'mb_detect_encoding' => 1,
        'mb_detect_order' => 0,
        'mb_decode_numericentity' => 1,
        'mb_encode_numericentity' => 1,
        'mb_ereg' => 2,
        'mb_eregi' => 2,
        'mb_parse_str' => 1,
        'min' => 0,
        'msg_set_queue' => 1,
        'mysql_fetch_object' => 2,
        'mysqli_fetch_object' => 2,
        'natcasesort' => 0,
        'natsort' => 0,
        'ncurses_getmouse' => 0,
        'ncurses_ungetmouse' => 0,
        'next' => 0,
        'openssl_csr_new' => 0,
        'openssl_csr_sign' => 4,
        'openssl_pkcs12_export' => 4,
        'openssl_pkcs12_export_to_file' => 4,
        'openssl_pkcs12_read' => 1,
        'openssl_pkcs7_encrypt' => 3,
        'openssl_pkcs7_sign' => 4,
        'openssl_pkcs7_verify' => 3,
        'openssl_pkey_export' => 3,
        'openssl_pkey_export_to_file' => 3,
        'openssl_pkey_new' => 0,
        'openssl_seal' => 2,
        'openssl_x509_checkpurpose' => 2,
        'pack' => 1,
        'parse_str' => 1,
        'pcntl_exec' => 1,
        'pg_convert' => 2,
        'pg_copy_from' => 2,
        'pg_delete' => 2,
        'pg_execute' => 2,
        'pg_insert' => 2,
        'pg_query_params' => 2,
        'pg_select' => 2,
        'pg_send_execute' => 2,
        'pg_send_query_params' => 2,
        'pg_update' => 2,
        'pos' => 0,
        'preg_grep' => 1,
        'preg_match' => 2,
        'preg_match_all' => 2,
        'prev' => 0,
        'preg_replace' => 2,
        'preg_replace_callback' => 2,
        'proc_open' => 1,
        'print_r' => 0,
        'register_shutdown_function' => 1,
        'register_tick_function' => 1,
        'reset' => 0,
        'rsort' => 0,
        'serialize' => 0,
        'session_register' => 0,
        'settype' => 0,
        'shuffle' => 0,
        'sizeof' => 0,
        'socket_create_pair' => 3,
        'socket_select' => 0,
        'sort' => 0,
        'str_ireplace' => 2,
        'str_replace' => 2,
        'stream_context_create' => 0,
        'stream_context_get_default' => 0,
        'stream_context_set_params' => 1,
        'stream_select' => 0,
        'substr_replace' => 0,
        'uasort' => 0,
        'uksort' => 0,
        'usort' => 0,
        'var_dump' => 0,
        'var_export' => 0,
        'vfprintf' => 2,
        'vprintf' => 1,
        'vsprintf' => 1,
        'wddx_add_vars' => 1,
        'wddx_serialize_value' => 0,
        'wddx_serialize_vars' => 0,
        'xml_parse_into_struct' => 2, 
   );

    /**
     * implements ArrayAccess
     **/
    public function offsetExists($offset)
    {
        return isset($this->data[$this->revert($offset)]);
    }

    public function offsetGet($offset)
    {
        return self::factory(&$this->data[$this->revert($offset)]);        
    }

    public function offsetSet($offset, $value)
    {
        $offset = $this->revert($offset);
        $value  = $this->revert($value);
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$this->revert($offset)]);  
    }

    /**
     * implements Iterator
     **/
    public function rewind()
    {
        $this->__call('reset');
    }

    public function current()
    {
        return $this->__call('current');
    }

    public function key()
    {
        //return $this->__call('key');
        return key($this->data);
    }

    public function next()
    {
        return $this->__call('next');
    }

    public function valid()
    {
        // return $this->__call(current($this->data) !== false);
        return current($this->data) !== false;
    }

}
