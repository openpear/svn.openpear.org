<?php
require_once "PHP/Object.php";

class PHP_Object_Boolean extends PHP_Object
{
    protected $argOffsets =  array(
        'array_chunk' => 2,
        'array_fill' => 2,
        'array_fill_keys' => 1,
        'array_keys' => 2,
        'array_pad' => 2,
        'array_push' => 1,
        'array_reverse' => 1,
        'array_search' => 2,
        'array_slice' => 3,
        'array_splice' => 3,
        'array_unshift' => 1,
        'assert' => 0,
        'assert_options' => 1,
        'base64_decode' => 1,
        'call_user_func' => 1,
        'call_user_method' => 2,
        'class_exists' => 1,
        'class_implements' => 1,
        'class_parents' => 1,
        'clearstatcache' => 0,
        'dba_handlers' => 0,
        'debug_backtrace' => 0,
        'debug_zval_dump' => 0,
        'define' => 1,
        'doubleval' => 0,
        'exif_read_data' => 2,
        'filter_var' => 0,
        'floatval' => 0,
        'fopen' => 2,
        'ftp_pasv' => 1,
        'ftp_rawlist' => 2,
        'get_browser' => 1,
        'get_defined_constants' => 0,
        'get_loaded_extensions' => 0,
        'get_meta_tags' => 1,
        'gettimeofday' => 0,
        'gettype' => 0,
        'hash' => 2,
        'hash_file' => 2,
        'hash_final' => 1,
        'hash_hmac' => 3,
        'hash_hmac_file' => 3,
        'header' => 1,
        'highlight_file' => 1,
        'highlight_string' => 1,
        'htmlentities' => 3,
        'htmlspecialchars' => 3,
        'image_type_to_extension' => 1,
        'in_array' => 2,
        'ini_get_all' => 1,
        'interface_exists' => 1,
        'intval' => 0,
        'is_array' => 0,
        'is_bool' => 0,
        'is_callable' => 1,
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
        'iterator_to_array' => 1,
        'jdtojewish' => 1,
        'json_decode' => 1,
        'json_encode' => 0,
        'libxml_use_internal_errors' => 0,
        'localtime' => 1,
        'mb_detect_encoding' => 2,
        'mb_stristr' => 2,
        'mb_strrchr' => 2,
        'mb_strrichr' => 2,
        'mb_strstr' => 2,
        'md5' => 1,
        'md5_file' => 1,
        'memory_get_peak_usage' => 0,
        'memory_get_usage' => 0,
        'microtime' => 0,
        'mkdir' => 2,
        'msg_receive' => 5,
        'msg_send' => 3,
        'mysql_connect' => 3,
        'mysqli_autocommit' => 1,
        'ncurses_keyok' => 1,
        'ncurses_keypad' => 1,
        'ncurses_meta' => 1,
        'ncurses_mouse_trafo' => 2,
        'ncurses_use_env' => 0,
        'ncurses_use_extended_names' => 0,
        'ncurses_wmouse_trafo' => 3,
        'nl2br' => 1,
        'ob_get_status' => 0,
        'ob_start' => 2,
        'openssl_csr_export' => 2,
        'openssl_csr_export_to_file' => 2,
        'openssl_csr_get_public_key' => 1,
        'openssl_csr_get_subject' => 1,
        'openssl_x509_export' => 2,
        'openssl_x509_export_to_file' => 2,
        'openssl_x509_parse' => 1,
        'pack' => 1,
        'parse_ini_file' => 1,
        'pcntl_signal' => 2,
        'pg_field_table' => 2,
        'print_r' => 0,
        'read_exif_data' => 2,
        'readfile' => 1,
        'register_shutdown_function' => 1,
        'register_tick_function' => 1,
        'serialize' => 0,
        'session_regenerate_id' => 0,
        'session_set_cookie_params' => 3,
        'setcookie' => 5,
        'setrawcookie' => 5,
        'settype' => 0,
        'sha1' => 1,
        'sha1_file' => 1,
        'show_source' => 1,
        'simplexml_load_file' => 4,
        'simplexml_load_string' => 4,
        'stream_socket_enable_crypto' => 1,
        'stream_socket_get_name' => 1,
        'strchr' => 2,
        'stristr' => 2,
        'strstr' => 2,
        'strval' => 0,
        'substr_compare' => 4,
        'uniqid' => 1,
        'use_soap_error_handler' => 0,
        'var_dump' => 0,
        'var_export' => 0,
        'wddx_serialize_value' => 0,
        'wordwrap' => 3,
        'xml_parse' => 2,
        'XMLWRITER_FLUSH' => 1,
        'XMLWRITER_OUTPUT_MEMORY' => 1,
        'XMLWRITER_SET_INDENT' => 1,
        'XMLWRITER_START_DTD_ENTITY' => 2,
    );

}
