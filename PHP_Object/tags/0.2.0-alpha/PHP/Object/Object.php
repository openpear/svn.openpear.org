<?php
require_once "PHP/Object.php";

class PHP_Object_Object extends PHP_Object
{
    protected $argOffsets =  array(
        'array_fill' => 2,
        'array_fill_keys' => 1,
        'array_pad' => 2,
        'array_push' => 1,
        'array_search' => 0,
        'array_splice' => 3,
        'array_unshift' => 1,
        'assert' => 0,
        'call_user_func' => 1,
        'call_user_method' => 1,
        'call_user_method_array' => 1,
        'class_implements' => 0,
        'class_parents' => 0,
        'count' => 0,
        'date_create' => 1,
        'date_date_set' => 0,
        'date_format' => 0,
        'date_isodate_set' => 0,
        'date_modify' => 0,
        'date_offset_get' => 0,
        'date_time_set' => 0,
        'date_timezone_get' => 0,
        'date_timezone_set' => 0,
        'debug_zval_dump' => 0,
        'dom_import_simplexml' => 0,
        'filter_var' => 0,
        'get_class' => 0,
        'get_class_methods' => 0,
        'get_object_vars' => 0,
        'get_parent_class' => 0,
        'gettype' => 0,
        'in_array' => 0,
        'intval' => 0,
        'is_a' => 0,
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
        'is_soap_fault' => 0,
        'is_string' => 0,
        'is_subclass_of' => 0,
        'iterator_apply' => 0,
        'iterator_count' => 0,
        'iterator_to_array' => 0,
        'json_encode' => 0,
        'mb_convert_variables' => 2,
        'method_exists' => 0,
        'mysqli_affected_rows' => 0,
        'mysqli_autocommit' => 0,
        'mysqli_bind_param' => 0,
        'mysqli_bind_result' => 0,
        'mysqli_change_user' => 0,
        'mysqli_character_set_name' => 0,
        'mysqli_client_encoding' => 0,
        'mysqli_close' => 0,
        'mysqli_commit' => 0,
        'mysqli_data_seek' => 0,
        'mysqli_disable_reads_from_master' => 0,
        'mysqli_disable_rpl_parse' => 0,
        'mysqli_dump_debug_info' => 0,
        'mysqli_enable_reads_from_master' => 0,
        'mysqli_enable_rpl_parse' => 0,
        'mysqli_errno' => 0,
        'mysqli_error' => 0,
        'mysqli_escape_string' => 0,
        'mysqli_fetch_array' => 0,
        'mysqli_fetch_assoc' => 0,
        'mysqli_fetch_field' => 0,
        'mysqli_fetch_field_direct' => 0,
        'mysqli_fetch_fields' => 0,
        'mysqli_fetch_lengths' => 0,
        'mysqli_fetch_object' => 0,
        'mysqli_fetch_row' => 0,
        'mysqli_field_count' => 0,
        'mysqli_field_seek' => 0,
        'mysqli_field_tell' => 0,
        'mysqli_free_result' => 0,
        'mysqli_get_charset' => 0,
        'mysqli_get_host_info' => 0,
        'mysqli_get_proto_info' => 0,
        'mysqli_get_server_info' => 0,
        'mysqli_get_server_version' => 0,
        'mysqli_get_warnings' => 0,
        'mysqli_info' => 0,
        'mysqli_insert_id' => 0,
        'mysqli_kill' => 0,
        'mysqli_master_query' => 0,
        'mysqli_more_results' => 0,
        'mysqli_multi_query' => 0,
        'mysqli_next_result' => 0,
        'mysqli_num_fields' => 0,
        'mysqli_num_rows' => 0,
        'mysqli_options' => 0,
        'mysqli_ping' => 0,
        'mysqli_prepare' => 0,
        'mysqli_query' => 0,
        'mysqli_real_connect' => 0,
        'mysqli_real_escape_string' => 0,
        'mysqli_real_query' => 0,
        'mysqli_rollback' => 0,
        'mysqli_rpl_parse_enabled' => 0,
        'mysqli_rpl_probe' => 0,
        'mysqli_rpl_query_type' => 0,
        'mysqli_select_db' => 0,
        'mysqli_send_query' => 0,
        'mysqli_set_charset' => 0,
        'mysqli_set_local_infile_default' => 0,
        'mysqli_set_local_infile_handler' => 0,
        'mysqli_set_opt' => 0,
        'mysqli_slave_query' => 0,
        'mysqli_sqlstate' => 0,
        'mysqli_ssl_set' => 0,
        'mysqli_stat' => 0,
        'mysqli_stmt_affected_rows' => 0,
        'mysqli_stmt_attr_get' => 0,
        'mysqli_stmt_attr_set' => 0,
        'mysqli_stmt_bind_param' => 0,
        'mysqli_stmt_bind_result' => 0,
        'mysqli_stmt_close' => 0,
        'mysqli_stmt_data_seek' => 0,
        'mysqli_stmt_errno' => 0,
        'mysqli_stmt_error' => 0,
        'mysqli_stmt_execute' => 0,
        'mysqli_stmt_fetch' => 0,
        'mysqli_stmt_field_count' => 0,
        'mysqli_stmt_free_result' => 0,
        'mysqli_stmt_get_warnings' => 0,
        'mysqli_stmt_init' => 0,
        'mysqli_stmt_insert_id' => 0,
        'mysqli_stmt_num_rows' => 0,
        'mysqli_stmt_param_count' => 0,
        'mysqli_stmt_prepare' => 0,
        'mysqli_stmt_reset' => 0,
        'mysqli_stmt_result_metadata' => 0,
        'mysqli_stmt_send_long_data' => 0,
        'mysqli_stmt_sqlstate' => 0,
        'mysqli_stmt_store_result' => 0,
        'mysqli_store_result' => 0,
        'mysqli_thread_id' => 0,
        'mysqli_use_result' => 0,
        'mysqli_warning_count' => 0,
        'pack' => 1,
        'print_r' => 0,
        'property_exists' => 0,
        'register_shutdown_function' => 1,
        'register_tick_function' => 1,
        'serialize' => 0,
        'settype' => 0,
        'simplexml_import_dom' => 0,
        'sizeof' => 0,
        'spl_object_hash' => 0,
        'timezone_name_get' => 0,
        'timezone_offset_get' => 0,
        'timezone_transitions_get' => 0,
        'var_dump' => 0,
        'var_export' => 0,
        'wddx_serialize_value' => 0,
        'xml_set_object' => 1,
    );

    public function __clone()
    {
        $this->data = clone $this->data;
    } 
}
