<?php
require_once "PHP/Object.php";

class PHP_Object_Resource extends PHP_Object
{
    protected $argOffsets = array(
        'array_fill' => 2,
        'array_fill_keys' => 1,
        'array_pad' => 2,
        'array_push' => 1,
        'array_search' => 0,
        'array_splice' => 3,
        'array_unshift' => 1,
        'assert' => 0,
        'bzclose' => 0,
        'bzerrno' => 0,
        'bzerror' => 0,
        'bzerrstr' => 0,
        'bzflush' => 0,
        'bzread' => 0,
        'bzwrite' => 0,
        'call_user_func' => 1,
        'call_user_method' => 2,
        'closedir' => 0,
        'copy' => 2,
        'debug_zval_dump' => 0,
        'dba_close' => 0,
        'dba_delete' => 1,
        'dba_exists' => 1,
        'dba_fetch' => 1,
        'dba_firstkey' => 0,
        'dba_insert' => 2,
        'dba_nextkey' => 0,
        'dba_optimize' => 0,
        'dba_replace' => 2,
        'dba_sync' => 0,
        'fclose' => 0,
        'feof' => 0,
        'fflush' => 0,
        'fgetc' => 0,
        'fgetcsv' => 0,
        'fgets' => 0,
        'fgetss' => 0,
        'file' => 2,
        'file_get_contents' => 2,
        'file_put_contents' => 3,
        'filter_var' => 0,
        'floatval' => 0,
        'flock' => 0,
        'fopen' => 3,
        'fpassthru' => 0,
        'fprintf' => 0,
        'fputcsv' => 0,
        'fputs' => 0,
        'fread' => 0,
        'fscanf' => 0,
        'fseek' => 0,
        'fstat' => 0,
        'ftell' => 0,
        'ftp_alloc' => 0,
        'ftp_cdup' => 0,
        'ftp_chdir' => 0,
        'ftp_chmod' => 0,
        'ftp_close' => 0,
        'ftp_delete' => 0,
        'ftp_exec' => 0,
        'ftp_fget' => 0,
        'ftp_fput' => 0,
        'ftp_get' => 0,
        'ftp_get_option' => 0,
        'ftp_login' => 0,
        'ftp_mdtm' => 0,
        'ftp_mkdir' => 0,
        'ftp_nb_continue' => 0,
        'ftp_nb_fget' => 0,
        'ftp_nb_fput' => 0,
        'ftp_nb_get' => 0,
        'ftp_nb_put' => 0,
        'ftp_nlist' => 0,
        'ftp_pasv' => 0,
        'ftp_put' => 0,
        'ftp_pwd' => 0,
        'ftp_quit' => 0,
        'ftp_raw' => 0,
        'ftp_rawlist' => 0,
        'ftp_rename' => 0,
        'ftp_rmdir' => 0,
        'ftp_set_option' => 0,
        'ftp_site' => 0,
        'ftp_size' => 0,
        'ftp_systype' => 0,
        'ftruncate' => 0,
        'fwrite' => 0,
        'get_resource_type' => 0,
        'gettype' => 0,
        'gzclose' => 0,
        'gzeof' => 0,
        'gzgetc' => 0,
        'gzgets' => 0,
        'gzgetss' => 0,
        'gzpassthru' => 0,
        'gzputs' => 0,
        'gzread' => 0,
        'gzrewind' => 0,
        'gzseek' => 0,
        'gztell' => 0,
        'gzwrite' => 0,
        'hash_final' => 0,
        'hash_update' => 0,
        'hash_update_file' => 0,
        'hash_update_stream' => 0,
        'in_array' => 0,
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
        'libxml_set_streams_context' => 0,
        'mkdir' => 3,
        'msg_receive' => 0,
        'msg_remove_queue' => 0,
        'msg_send' => 0,
        'msg_set_queue' => 0,
        'msg_stat_queue' => 0,
        'mysql_affected_rows' => 0,
        'mysql_client_encoding' => 0,
        'mysql_close' => 0,
        'mysql_data_seek' => 0,
        'mysql_db_name' => 0,
        'mysql_db_query' => 2,
        'mysql_errno' => 0,
        'mysql_error' => 0,
        'mysql_fetch_array' => 0,
        'mysql_fetch_assoc' => 0,
        'mysql_fetch_field' => 0,
        'mysql_fetch_lengths' => 0,
        'mysql_fetch_object' => 0,
        'mysql_fetch_row' => 0,
        'mysql_field_flags' => 0,
        'mysql_field_len' => 0,
        'mysql_field_name' => 0,
        'mysql_field_seek' => 0,
        'mysql_field_table' => 0,
        'mysql_field_type' => 0,
        'mysql_free_result' => 0,
        'mysql_get_host_info' => 0,
        'mysql_get_proto_info' => 0,
        'mysql_get_server_info' => 0,
        'mysql_info' => 0,
        'mysql_insert_id' => 0,
        'mysql_list_dbs' => 0,
        'mysql_list_fields' => 2,
        'mysql_list_processes' => 0,
        'mysql_list_tables' => 1,
        'mysql_num_fields' => 0,
        'mysql_num_rows' => 0,
        'mysql_ping' => 0,
        'mysql_query' => 1,
        'mysql_real_escape_string' => 1,
        'mysql_result' => 0,
        'mysql_select_db' => 1,
        'mysql_set_charset' => 1,
        'mysql_stat' => 0,
        'mysql_tablename' => 0,
        'mysql_thread_id' => 0,
        'mysql_unbuffered_query' => 1,
        'ncurses_bottom_panel' => 0,
        'ncurses_del_panel' => 0,
        'ncurses_delwin' => 0,
        'ncurses_getmaxyx' => 0,
        'ncurses_getyx' => 0,
        'ncurses_hide_panel' => 0,
        'ncurses_keypad' => 0,
        'ncurses_meta' => 0,
        'ncurses_move_panel' => 0,
        'ncurses_mvwaddstr' => 0,
        'ncurses_new_panel' => 0,
        'ncurses_panel_above' => 0,
        'ncurses_panel_below' => 0,
        'ncurses_panel_window' => 0,
        'ncurses_pnoutrefresh' => 0,
        'ncurses_prefresh' => 0,
        'ncurses_replace_panel' => 0,
        'ncurses_show_panel' => 0,
        'ncurses_top_panel' => 0,
        'ncurses_waddch' => 0,
        'ncurses_waddstr' => 0,
        'ncurses_wattroff' => 0,
        'ncurses_wattron' => 0,
        'ncurses_wattrset' => 0,
        'ncurses_wborder' => 0,
        'ncurses_wclear' => 0,
        'ncurses_wcolor_set' => 0,
        'ncurses_werase' => 0,
        'ncurses_wgetch' => 0,
        'ncurses_whline' => 0,
        'ncurses_wmouse_trafo' => 0,
        'ncurses_wmove' => 0,
        'ncurses_wnoutrefresh' => 0,
        'ncurses_wrefresh' => 0,
        'ncurses_wstandend' => 0,
        'ncurses_wstandout' => 0,
        'ncurses_wvline' => 0,
        'opendir' => 1,
        'openssl_csr_export' => 0,
        'openssl_csr_export_to_file' => 0,
        'openssl_csr_new' => 1,
        'openssl_free_key' => 0,
        'openssl_pkey_free' => 0,
        'openssl_pkey_get_details' => 0,
        'openssl_x509_free' => 0,
        'pack' => 1,
        'pclose' => 0,
        'pg_affected_rows' => 0,
        'pg_cancel_query' => 0,
        'pg_client_encoding' => 0,
        'pg_close' => 0,
        'pg_connection_busy' => 0,
        'pg_connection_reset' => 0,
        'pg_connection_status' => 0,
        'pg_convert' => 0,
        'pg_copy_from' => 0,
        'pg_copy_to' => 0,
        'pg_dbname' => 0,
        'pg_delete' => 0,
        'pg_end_copy' => 0,
        'pg_escape_bytea' => 0,
        'pg_escape_string' => 0,
        'pg_execute' => 0,
        'pg_fetch_all' => 0,
        'pg_fetch_all_columns' => 0,
        'pg_fetch_array' => 0,
        'pg_fetch_assoc' => 0,
        'pg_fetch_object' => 0,
        'pg_fetch_result' => 0,
        'pg_fetch_row' => 0,
        'pg_field_is_null' => 0,
        'pg_field_name' => 0,
        'pg_field_num' => 0,
        'pg_field_prtlen' => 0,
        'pg_field_size' => 0,
        'pg_field_table' => 0,
        'pg_field_type' => 0,
        'pg_field_type_oid' => 0,
        'pg_free_result' => 0,
        'pg_get_notify' => 0,
        'pg_get_pid' => 0,
        'pg_get_result' => 0,
        'pg_host' => 0,
        'pg_insert' => 0,
        'pg_last_error' => 0,
        'pg_last_notice' => 0,
        'pg_last_oid' => 0,
        'pg_lo_close' => 0,
        'pg_lo_create' => 0,
        'pg_lo_export' => 0,
        'pg_lo_import' => 0,
        'pg_lo_open' => 0,
        'pg_lo_read' => 0,
        'pg_lo_read_all' => 0,
        'pg_lo_seek' => 0,
        'pg_lo_tell' => 0,
        'pg_lo_unlink' => 0,
        'pg_lo_write' => 0,
        'pg_meta_data' => 0,
        'pg_num_fields' => 0,
        'pg_num_rows' => 0,
        'pg_options' => 0,
        'pg_parameter_status' => 0,
        'pg_ping' => 0,
        'pg_port' => 0,
        'pg_prepare' => 0,
        'pg_query_params' => 0,
        'pg_result_error' => 0,
        'pg_result_error_field' => 0,
        'pg_result_seek' => 0,
        'pg_result_status' => 0,
        'pg_select' => 0,
        'pg_send_execute' => 0,
        'pg_send_prepare' => 0,
        'pg_send_query' => 0,
        'pg_send_query_params' => 0,
        'pg_set_error_verbosity' => 0,
        'pg_trace' => 2,
        'pg_transaction_status' => 0,
        'pg_tty' => 0,
        'pg_untrace' => 0,
        'pg_update' => 0,
        'pg_version' => 0,
        'proc_close' => 0,
        'proc_get_status' => 0,
        'proc_terminate' => 0,
        'readdir' => 0,
        'readfile' => 2,
        'register_shutdown_function' => 1,
        'register_tick_function' => 1,
        'rename' => 2,
        'rewind' => 0,
        'rewinddir' => 0,
        'rmdir' => 1,
        'scandir' => 2,
        'sem_acquire' => 0,
        'sem_release' => 0,
        'sem_remove' => 0,
        'set_file_buffer' => 0,
        'settype' => 0,
        'socket_accept' => 0,
        'socket_bind' => 0,
        'socket_clear_error' => 0,
        'socket_close' => 0,
        'socket_connect' => 0,
        'socket_get_option' => 0,
        'socket_get_status' => 0,
        'socket_getpeername' => 0,
        'socket_getsockname' => 0,
        'socket_last_error' => 0,
        'socket_listen' => 0,
        'socket_read' => 0,
        'socket_recv' => 0,
        'socket_recvfrom' => 0,
        'socket_send' => 0,
        'socket_sendto' => 0,
        'socket_set_block' => 0,
        'socket_set_blocking' => 0,
        'socket_set_nonblock' => 0,
        'socket_set_option' => 0,
        'socket_set_timeout' => 0,
        'socket_shutdown' => 0,
        'socket_write' => 0,
        'stream_bucket_append' => 0,
        'stream_bucket_make_writeable' => 0,
        'stream_bucket_new' => 0,
        'stream_bucket_prepend' => 0,
        'stream_context_get_options' => 0,
        'stream_context_set_option' => 0,
        'stream_context_set_params' => 0,
        'stream_copy_to_stream' => 0,
        'stream_filter_append' => 0,
        'stream_filter_prepend' => 0,
        'stream_filter_remove' => 0,
        'stream_get_contents' => 0,
        'stream_get_line' => 0,
        'stream_get_meta_data' => 0,
        'stream_set_blocking' => 0,
        'stream_set_timeout' => 0,
        'stream_set_write_buffer' => 0,
        'stream_socket_accept' => 0,
        'stream_socket_client' => 5,
        'stream_socket_enable_crypto' => 0,
        'stream_socket_get_name' => 0,
        'stream_socket_recvfrom' => 0,
        'stream_socket_sendto' => 0,
        'stream_socket_server' => 4,
        'stream_socket_shutdown' => 0,
        'unlink' => 1,
        'var_dump' => 0,
        'var_export' => 0,
        'vfprintf' => 0,
        'wddx_add_vars' => 0,
        'wddx_packet_end' => 0,
        'wddx_serialize_value' => 0,
        'xml_get_current_byte_index' => 0,
        'xml_get_current_column_number' => 0,
        'xml_get_current_line_number' => 0,
        'xml_get_error_code' => 0,
        'xml_parse' => 0,
        'xml_parse_into_struct' => 0,
        'xml_parser_free' => 0,
        'xml_parser_get_option' => 0,
        'xml_parser_set_option' => 0,
        'xml_set_character_data_handler' => 0,
        'xml_set_default_handler' => 0,
        'xml_set_element_handler' => 0,
        'xml_set_end_namespace_decl_handler' => 0,
        'xml_set_external_entity_ref_handler' => 0,
        'xml_set_notation_decl_handler' => 0,
        'xml_set_object' => 0,
        'xml_set_processing_instruction_handler' => 0,
        'xml_set_start_namespace_decl_handler' => 0,
        'xml_set_unparsed_entity_decl_handler' => 0,
        'xmlwriter_end_attribute' => 0,
        'xmlwriter_end_cdata' => 0,
        'xmlwriter_end_comment' => 0,
        'xmlwriter_end_document' => 0,
        'xmlwriter_end_dtd' => 0,
        'xmlwriter_end_dtd_attlist' => 0,
        'xmlwriter_end_dtd_element' => 0,
        'xmlwriter_end_dtd_entity' => 0,
        'xmlwriter_end_element' => 0,
        'xmlwriter_end_pi' => 0,
        'xmlwriter_flush' => 0,
        'xmlwriter_full_end_element' => 0,
        'xmlwriter_output_memory' => 0,
        'xmlwriter_set_indent' => 0,
        'xmlwriter_set_indent_string' => 0,
        'xmlwriter_start_attribute' => 0,
        'xmlwriter_start_attribute_ns' => 0,
        'xmlwriter_start_cdata' => 0,
        'xmlwriter_start_comment' => 0,
        'xmlwriter_start_document' => 0,
        'xmlwriter_start_dtd' => 0,
        'xmlwriter_start_dtd_attlist' => 0,
        'xmlwriter_start_dtd_element' => 0,
        'xmlwriter_start_dtd_entity' => 0,
        'xmlwriter_start_element' => 0,
        'xmlwriter_start_element_ns' => 0,
        'xmlwriter_start_pi' => 0,
        'xmlwriter_text' => 0,
        'xmlwriter_write_attribute' => 0,
        'xmlwriter_write_attribute_ns' => 0,
        'xmlwriter_write_cdata' => 0,
        'xmlwriter_write_comment' => 0,
        'xmlwriter_write_dtd' => 0,
        'xmlwriter_write_dtd_attlist' => 0,
        'xmlwriter_write_dtd_element' => 0,
        'xmlwriter_write_dtd_entity' => 0,
        'xmlwriter_write_element' => 0,
        'xmlwriter_write_element_ns' => 0,
        'xmlwriter_write_pi' => 0,
        'xmlwriter_write_raw' => 0,
        'zip_close' => 0,
        'zip_entry_close' => 0,
        'zip_entry_compressedsize' => 0,
        'zip_entry_compressionmethod' => 0,
        'zip_entry_filesize' => 0,
        'zip_entry_name' => 0,
        'zip_entry_open' => 0,
        'zip_entry_read' => 0,
        'zip_read' => 0,
    );

}
