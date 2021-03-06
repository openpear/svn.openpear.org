<?php
require_once "PHP/Object.php";

class PHP_Object_String extends PHP_Object
{
    protected $aliasMethods = array(
        'str_*',
        'str*',
    );

    protected $argOffsets =  array(
        '_' => 0,
        'addcslashes' => 0,
        'addslashes' => 0,
        'array_fill' => 2,
        'array_fill_keys' => 1,
        'array_key_exists' => 0,
        'array_keys' => 1,
        'array_pad' => 2,
        'array_push' => 1,
        'array_search' => 0,
        'array_splice' => 3,
        'array_unshift' => 1,
        'assert' => 0,
        'assert_options' => 1,
        'base64_decode' => 0,
        'base64_encode' => 0,
        'base_convert' => 0,
        'basename' => 0,
        'bcadd' => 0,
        'bccomp' => 0,
        'bcdiv' => 0,
        'bcmod' => 0,
        'bcmul' => 0,
        'bcpow' => 0,
        'bcpowmod' => 0,
        'bcsqrt' => 0,
        'bcsub' => 0,
        'bin2hex' => 0,
        'bind_textdomain_codeset' => 0,
        'bindec' => 0,
        'bindtextdomain' => 0,
        'bzcompress' => 0,
        'bzdecompress' => 0,
        'bzopen' => 0,
        'bzwrite' => 1,
        'call_user_func' => 1,
        'call_user_method' => 2,
        'chdir' => 0,
        'checkdnsrr' => 0,
        'chgrp' => 0,
        'chmod' => 0,
        'chop' => 0,
        'chown' => 0,
        'chroot' => 0,
        'chunk_split' => 0,
        'class_exists' => 0,
        'class_implements' => 0,
        'class_parents' => 0,
        'clearstatcache' => 1,
        'compact' => 0,
        'constant' => 0,
        'convert_cyr_string' => 0,
        'convert_uudecode' => 0,
        'convert_uuencode' => 0,
        'copy' => 0,
        'count_chars' => 0,
        'crc32' => 0,
        'create_function' => 0,
        'crypt' => 0,
        'ctype_alnum' => 0,
        'ctype_alpha' => 0,
        'ctype_cntrl' => 0,
        'ctype_digit' => 0,
        'ctype_graph' => 0,
        'ctype_lower' => 0,
        'ctype_print' => 0,
        'ctype_punct' => 0,
        'ctype_space' => 0,
        'ctype_upper' => 0,
        'ctype_xdigit' => 0,
        'date' => 0,
        'date_create' => 0,
        'date_default_timezone_set' => 0,
        'date_format' => 1,
        'date_modify' => 1,
        'date_parse' => 0,
        'dba_delete' => 0,
        'dba_exists' => 0,
        'dba_fetch' => 0,
        'dba_insert' => 0,
        'dba_key_split' => 0,
        'dba_open' => 0,
        'dba_popen' => 0,
        'dba_replace' => 0,
        'dcgettext' => 0,
        'dcngettext' => 0,
        'debug_zval_dump' => 0,
        'define' => 1,
        'defined' => 0,
        'dgettext' => 0,
        'dir' => 0,
        'dirname' => 0,
        'disk_free_space' => 0,
        'disk_total_space' => 0,
        'diskfreespace' => 0,
        'dl' => 0,
        'dngettext' => 0,
        'dns_check_record' => 0,
        'dns_get_record' => 0,
        'dns_get_mx' => 0,
        'doubleval' => 0,
        'ereg' => 1,
        'ereg_replace' => 2,
        'eregi' => 1,
        'eregi_replace' => 2,
        'error_log' => 0,
        'escapeshellarg' => 0,
        'escapeshellcmd' => 0,
        'exec' => 0,
        'exif_imagetype' => 0,
        'exif_read_data' => 0,
        'exif_tagname' => 0,
        'exif_thumbnail' => 0,
        'explode' => 1,
        'extension_loaded' => 0,
        'extract' => 2,
        'ezmlm_hash' => 0,
        'fgetcsv' => 2,
        'fgetss' => 2,
        'file' => 0,
        'file_exists' => 0,
        'file_get_contents' => 0,
        'file_put_contents' => 1,
        'fileatime' => 0,
        'filectime' => 0,
        'filegroup' => 0,
        'fileinode' => 0,
        'filemtime' => 0,
        'fileowner' => 0,
        'fileperms' => 0,
        'filesize' => 0,
        'filetype' => 0,
        'filter_has_var' => 1,
        'filter_id' => 0,
        'filter_input' => 1,
        'filter_var' => 0,
        'floatval' => 0,
        'fnmatch' => 0,
        'fopen' => 0,
        'fprintf' => 1,
        'fputcsv' => 2,
        'fputs' => 1,
        'fscanf' => 1,
        'fsockopen' => 0,
        'ftok' => 0,
        'ftp_alloc' => 2,
        'ftp_chdir' => 1,
        'ftp_chmod' => 2,
        'ftp_connect' => 0,
        'ftp_delete' => 1,
        'ftp_exec' => 1,
        'ftp_fget' => 2,
        'ftp_fput' => 1,
        'ftp_get' => 1,
        'ftp_login' => 1,
        'ftp_mdtm' => 1,
        'ftp_mkdir' => 1,
        'ftp_nb_fget' => 2,
        'ftp_nb_fput' => 1,
        'ftp_nb_get' => 1,
        'ftp_nb_put' => 1,
        'ftp_nlist' => 1,
        'ftp_put' => 1,
        'ftp_raw' => 1,
        'ftp_rawlist' => 1,
        'ftp_rename' => 1,
        'ftp_rmdir' => 1,
        'ftp_site' => 1,
        'ftp_size' => 1,
        'ftp_ssl_connect' => 0,
        'function_exists' => 0,
        'fwrite' => 1,
        'get_browser' => 0,
        'get_cfg_var' => 0,
        'get_class_methods' => 0,
        'get_class_vars' => 0,
        'get_extension_funcs' => 0,
        'get_headers' => 0,
        'get_meta_tags' => 0,
        'get_parent_class' => 0,
        'getenv' => 0,
        'gethostbyaddr' => 0,
        'gethostbyname' => 0,
        'gethostbynamel' => 0,
        'getimagesize' => 0,
        'getmxrr' => 0,
        'getopt' => 0,
        'getprotobyname' => 0,
        'getservbyname' => 0,
        'getservbyport' => 1,
        'gettext' => 0,
        'gettype' => 0,
        'glob' => 0,
        'gmdate' => 0,
        'gmstrftime' => 0,
        'gzcompress' => 0,
        'gzdeflate' => 0,
        'gzencode' => 0,
        'gzfile' => 0,
        'gzgetss' => 2,
        'gzinflate' => 0,
        'gzopen' => 0,
        'gzputs' => 1,
        'gzuncompress' => 0,
        'gzwrite' => 1,
        'hash' => 0,
        'hash_file' => 0,
        'hash_hmac' => 0,
        'hash_hmac_file' => 0,
        'hash_init' => 0,
        'hash_update' => 1,
        'hash_update_file' => 1,
        'header' => 0,
        'headers_sent' => 0,
        'hebrev' => 0,
        'hebrevc' => 0,
        'hexdec' => 0,
        'highlight_file' => 0,
        'highlight_string' => 0,
        'html_entity_decode' => 0,
        'htmlentities' => 0,
        'htmlspecialchars' => 0,
        'htmlspecialchars_decode' => 0,
        'http_build_query' => 1,
        'iconv' => 0,
        'iconv_get_encoding' => 0,
        'iconv_mime_decode' => 0,
        'iconv_mime_decode_headers' => 0,
        'iconv_mime_encode' => 0,
        'iconv_set_encoding' => 0,
        'iconv_strlen' => 0,
        'iconv_strpos' => 0,
        'iconv_strrpos' => 0,
        'iconv_substr' => 0,
        'idate' => 0,
        'ignore_user_abort' => 0,
        'implode' => 0,
        'import_request_variables' => 0,
        'inet_ntop' => 0,
        'inet_pton' => 0,
        'in_array' => 0,
        'ini_alter' => 0,
        'ini_get' => 0,
        'ini_get_all' => 0,
        'ini_restore' => 0,
        'ini_set' => 0,
        'interface_exists' => 0,
        'intval' => 0,
        'ip2long' => 0,
        'iptcembed' => 0,
        'iptcparse' => 0,
        'is_a' => 1,
        'is_array' => 0,
        'is_bool' => 0,
        'is_callable' => 2,
        'is_dir' => 0,
        'is_double' => 0,
        'is_executable' => 0,
        'is_file' => 0,
        'is_float' => 0,
        'is_int' => 0,
        'is_integer' => 0,
        'is_link' => 0,
        'is_long' => 0,
        'is_null' => 0,
        'is_numeric' => 0,
        'is_object' => 0,
        'is_readable' => 0,
        'is_real' => 0,
        'is_resource' => 0,
        'is_scalar' => 0,
        'is_string' => 0,
        'is_subclass_of' => 0,
        'is_uploaded_file' => 0,
        'is_writable' => 0,
        'is_writeable' => 0,
        'join' => 0,
        'json_decode' => 0,
        'json_encode' => 0,
        'key_exists' => 0,
        'lchgrp' => 0,
        'lchown' => 0,
        'levenshtein' => 0,
        'link' => 0,
        'linkinfo' => 0,
        'lstat' => 0,
        'ltrim' => 0,
        'mail' => 0,
        'mb_check_encoding' => 0,
        'mb_convert_case' => 0,
        'mb_convert_encoding' => 0,
        'mb_convert_kana' => 0,
        'mb_convert_variables' => 2,
        'mb_decode_mimeheader' => 0,
        'mb_decode_numericentity' => 0,
        'mb_detect_encoding' => 0,
        'mb_detect_order' => 0,
        'mb_encode_mimeheader' => 0,
        'mb_encode_numericentity' => 0,
        'mb_ereg' => 1,
        'mb_ereg_match' => 1,
        'mb_ereg_replace' => 2,
        'mb_ereg_search' => 0,
        'mb_ereg_search_init' => 0,
        'mb_ereg_search_pos' => 0,
        'mb_ereg_search_regs' => 0,
        'mb_eregi' => 1,
        'mb_eregi_replace' => 2,
        'mb_get_info' => 0,
        'mb_http_input' => 0,
        'mb_http_output' => 0,
        'mb_internal_encoding' => 0,
        'mb_language' => 0,
        'mb_output_handler' => 0,
        'mb_parse_str' => 0,
        'mb_preferred_mime_name' => 0,
        'mb_regex_encoding' => 0,
        'mb_regex_set_options' => 0,
        'mb_send_mail' => 0,
        'mb_split' => 1,
        'mb_strcut' => 0,
        'mb_strimwidth' => 0,
        'mb_stripos' => 0,
        'mb_stristr' => 0,
        'mb_strlen' => 0,
        'mb_strpos' => 0,
        'mb_strrchr' => 0,
        'mb_strrichr' => 0,
        'mb_strripos' => 0,
        'mb_strrpos' => 0,
        'mb_strstr' => 0,
        'mb_strtolower' => 0,
        'mb_strtoupper' => 0,
        'mb_strwidth' => 0,
        'mb_substitute_character' => 0,
        'mb_substr' => 0,
        'mb_substr_count' => 0,
        'md5' => 0,
        'md5_file' => 0,
        'metaphone' => 0,
        'method_exists' => 0,
        'mime_content_type' => 0,
        'mkdir' => 0,
        'money_format' => 0,
        'move_uploaded_file' => 0,
        'msg_receive' => 4,
        'msg_send' => 2,
        'mysql_connect' => 0,
        'mysql_db_name' => 2,
        'mysql_db_query' => 0,
        'mysql_escape_string' => 0,
        'mysql_fetch_object' => 1,
        'mysql_list_fields' => 0,
        'mysql_list_tables' => 0,
        'mysql_pconnect' => 0,
        'mysql_query' => 0,
        'mysql_real_escape_string' => 0,
        'mysql_result' => 2,
        'mysql_select_db' => 0,
        'mysql_set_charset' => 0,
        'mysql_unbuffered_query' => 0,
        'mysqli_bind_param' => 1,
        'mysqli_change_user' => 1,
        'mysqli_connect' => 0,
        'mysqli_debug' => 0,
        'mysqli_escape_string' => 1,
        'mysqli_fetch_object' => 1,
        'mysqli_master_query' => 1,
        'mysqli_multi_query' => 1,
        'mysqli_options' => 2,
        'mysqli_prepare' => 1,
        'mysqli_query' => 1,
        'mysqli_real_connect' => 1,
        'mysqli_real_escape_string' => 1,
        'mysqli_real_query' => 1,
        'mysqli_rpl_query_type' => 1,
        'mysqli_select_db' => 1,
        'mysqli_send_query' => 1,
        'mysqli_set_charset' => 1,
        'mysqli_slave_query' => 1,
        'mysqli_ssl_set' => 1,
        'mysqli_stmt_bind_param' => 1,
        'mysqli_stmt_prepare' => 1,
        'mysqli_stmt_send_long_data' => 2,
        'ncurses_addchnstr' => 0,
        'ncurses_addchstr' => 0,
        'ncurses_addnstr' => 0,
        'ncurses_addstr' => 0,
        'ncurses_define_key' => 0,
        'ncurses_insstr' => 0,
        'ncurses_instr' => 0,
        'ncurses_mvaddchnstr' => 2,
        'ncurses_mvaddchstr' => 2,
        'ncurses_mvaddnstr' => 2,
        'ncurses_mvaddstr' => 2,
        'ncurses_mvwaddstr' => 3,
        'ncurses_putp' => 0,
        'ncurses_scr_dump' => 0,
        'ncurses_scr_init' => 0,
        'ncurses_scr_restore' => 0,
        'ncurses_scr_set' => 0,
        'ncurses_slk_set' => 1,
        'ncurses_waddstr' => 1,
        'ngettext' => 0,
        'nl2br' => 0,
        'ob_gzhandler' => 0,
        'ob_iconv_handler' => 0,
        'octdec' => 0,
        'opendir' => 0,
        'openlog' => 0,
        'openssl_csr_export' => 1,
        'openssl_csr_export_to_file' => 1,
        'openssl_get_privatekey' => 1,
        'openssl_open' => 0,
        'openssl_pkcs12_export' => 1,
        'openssl_pkcs12_export_to_file' => 1,
        'openssl_pkcs12_read' => 2,
        'openssl_pkcs7_decrypt' => 0,
        'openssl_pkcs7_encrypt' => 0,
        'openssl_pkcs7_sign' => 0,
        'openssl_pkcs7_verify' => 0,
        'openssl_pkey_export' => 1,
        'openssl_pkey_export_to_file' => 1,
        'openssl_pkey_get_private' => 1,
        'openssl_private_decrypt' => 0,
        'openssl_private_encrypt' => 0,
        'openssl_public_decrypt' => 0,
        'openssl_public_encrypt' => 0,
        'openssl_seal' => 0,
        'openssl_sign' => 0,
        'openssl_verify' => 0,
        'openssl_x509_checkpurpose' => 3,
        'openssl_x509_export' => 1,
        'openssl_x509_export_to_file' => 1,
        'ord' => 0,
        'output_add_rewrite_var' => 0,
        'pack' => 1,
        'parse_ini_file' => 0,
        'parse_str' => 0,
        'parse_url' => 0,
        'passthru' => 0,
        'pathinfo' => 0,
        'pcntl_exec' => 0,
        'pfsockopen' => 0,
        'pg_connect' => 0,
        'pg_convert' => 1,
        'pg_copy_from' => 1,
        'pg_copy_to' => 1,
        'pg_delete' => 1,
        'pg_escape_bytea' => 1,
        'pg_escape_string' => 1,
        'pg_execute' => 1,
        'pg_fetch_result' => 1,
        'pg_field_is_null' => 1,
        'pg_field_num' => 1,
        'pg_field_prtlen' => 1,
        'pg_insert' => 1,
        'pg_lo_export' => 2,
        'pg_lo_import' => 1,
        'pg_lo_open' => 2,
        'pg_lo_write' => 1,
        'pg_meta_data' => 1,
        'pg_parameter_status' => 1,
        'pg_pconnect' => 0,
        'pg_prepare' => 1,
        'pg_put_line' => 0,
        'pg_query' => 0,
        'pg_query_params' => 1,
        'pg_select' => 1,
        'pg_send_execute' => 1,
        'pg_send_prepare' => 1,
        'pg_send_query' => 1,
        'pg_send_query_params' => 1,
        'pg_set_client_encoding' => 0,
        'pg_trace' => 0,
        'pg_unescape_bytea' => 0,
        'pg_update' => 1,
        'php_strip_whitespace' => 0,
        'php_uname' => 0,
        'phpversion' => 0,
        'popen' => 0,
        'posix_access' => 0,
        'posix_getgrnam' => 0,
        'posix_getpwnam' => 0,
        'posix_initgroups' => 0,
        'posix_mkfifo' => 0,
        'posix_mknod' => 0,
        'preg_grep' => 1,
        'preg_match' => 1,
        'preg_match_all' => 1,
        'preg_quote' => 0,
        'preg_replace' => 2,
        'preg_split' => 1,
        'print_r' => 0,
        'printf' => 1,
        'proc_open' => 0,
        'property_exists' => 1,
        'putenv' => 0,
        'quoted_printable_decode' => 0,
        'quotemeta' => 0,
        'range' => 0,
        'rawurldecode' => 0,
        'rawurlencode' => 0,
        'read_exif_data' => 0,
        'readfile' => 0,
        'readgzfile' => 0,
        'readline' => 0,
        'readline_add_history' => 0,
        'readline_info' => 0,
        'readline_read_history' => 0,
        'readline_write_history' => 0,
        'readlink' => 0,
        'realpath' => 0,
        'register_shutdown_function' => 1,
        'register_tick_function' => 1,
        'rename' => 0,
        'rmdir' => 0,
        'rtrim' => 0,
        'scandir' => 0,
        'serialize' => 0,
        'session_cache_limiter' => 0,
        'session_decode' => 0,
        'session_id' => 0,
        'session_is_registered' => 0,
        'session_module_name' => 0,
        'session_name' => 0,
        'session_register' => 0,
        'session_save_path' => 0,
        'session_set_cookie_params' => 1,
        'session_unregister' => 0,
        'set_include_path' => 0,
        'setcookie' => 0,
        'setlocale' => 1,
        'setrawcookie' => 0,
        'settype' => 0,
        'sha1' => 0,
        'sha1_file' => 0,
        'shell_exec' => 0,
        'shmop_open' => 1,
        'shmop_write' => 1,
        'show_source' => 0,
        'similar_text' => 0,
        'simplexml_import_dom' => 1,
        'simplexml_load_file' => 0,
        'simplexml_load_string' => 0,
        'socket_bind' => 1,
        'socket_connect' => 1,
        'socket_getpeername' => 1,
        'socket_getsockname' => 1,
        'socket_recv' => 1,
        'socket_recvfrom' => 1,
        'socket_send' => 1,
        'socket_sendto' => 1,
        'socket_write' => 1,
        'soundex' => 0,
        'spl_autoload' => 0,
        'spl_autoload_call' => 0,
        'spl_autoload_extensions' => 0,
        'split' => 1,
        'spliti' => 1,
        'sprintf' => 1,
        'sql_regcase' => 0,
        'sscanf' => 0,
        'stat' => 0,
        'str_ireplace' => 2,
        'str_pad' => 0,
        'str_repeat' => 0,
        'str_replace' => 2,
        'str_rot13' => 0,
        'str_shuffle' => 0,
        'str_split' => 0,
        'str_word_count' => 0,
        'strcasecmp' => 0,
        'strchr' => 0,
        'strcmp' => 0,
        'strcoll' => 0,
        'strcspn' => 0,
        'stream_bucket_new' => 1,
        'stream_context_set_option' => 1,
        'stream_filter_append' => 1,
        'stream_filter_prepend' => 1,
        'stream_filter_register' => 0,
        'stream_get_line' => 2,
        'stream_register_wrapper' => 0,
        'stream_socket_accept' => 2,
        'stream_socket_client' => 0,
        'stream_socket_recvfrom' => 3,
        'stream_socket_sendto' => 1,
        'stream_socket_server' => 0,
        'stream_wrapper_register' => 0,
        'stream_wrapper_restore' => 0,
        'stream_wrapper_unregister' => 0,
        'strftime' => 0,
        'strip_tags' => 0,
        'stripcslashes' => 0,
        'stripos' => 0,
        'stripslashes' => 0,
        'stristr' => 0,
        'strlen' => 0,
        'strnatcasecmp' => 0,
        'strnatcmp' => 0,
        'strncasecmp' => 0,
        'strncmp' => 0,
        'strpbrk' => 0,
        'strpos' => 0,
        'strptime' => 0,
        'strrchr' => 0,
        'strrev' => 0,
        'strripos' => 0,
        'strrpos' => 0,
        'strspn' => 0,
        'strstr' => 0,
        'strtok' => 0,
        'strtolower' => 0,
        'strtotime' => 0,
        'strtoupper' => 0,
        'strtr' => 0,
        'strval' => 0,
        'substr' => 0,
        'substr_compare' => 0,
        'substr_count' => 0,
        'substr_replace' => 0,
        'symlink' => 0,
        'syslog' => 1,
        'system' => 0,
        'tempnam' => 0,
        'textdomain' => 0,
        'timezone_identifiers_list' => 1,
        'timezone_name_from_abbr' => 0,
        'timezone_open' => 0,
        'token_get_all' => 0,
        'touch' => 0,
        'trigger_error' => 0,
        'trim' => 0,
        'ucfirst' => 0,
        'ucwords' => 0,
        'uniqid' => 0,
        'unlink' => 0,
        'unpack' => 0,
        'unregister_tick_function' => 0,
        'unserialize' => 0,
        'urldecode' => 0,
        'urlencode' => 0,
        'user_error' => 0,
        'utf8_decode' => 0,
        'utf8_encode' => 0,
        'var_dump' => 0,
        'var_export' => 0,
        'version_compare' => 0,
        'vfprintf' => 1,
        'vprintf' => 0,
        'vsprintf' => 0,
        'wddx_add_vars' => 1,
        'wddx_deserialize' => 0,
        'wddx_packet_start' => 0,
        'wddx_serialize_value' => 0,
        'wddx_serialize_vars' => 0,
        'wordwrap' => 0,
        'xml_parse' => 1,
        'xml_parse_into_struct' => 1,
        'xml_parser_create' => 0,
        'xml_parser_create_ns' => 0,
        'xmlwriter_open_uri' => 0,
        'xmlwriter_set_indent_string' => 1,
        'xmlwriter_start_attribute' => 1,
        'xmlwriter_start_attribute_ns' => 1,
        'xmlwriter_start_document' => 1,
        'xmlwriter_start_dtd' => 1,
        'xmlwriter_start_dtd_attlist' => 1,
        'xmlwriter_start_dtd_element' => 1,
        'xmlwriter_start_dtd_entity' => 1,
        'xmlwriter_start_element' => 1,
        'xmlwriter_start_element_ns' => 1,
        'xmlwriter_start_pi' => 1,
        'xmlwriter_text' => 1,
        'xmlwriter_write_attribute' => 1,
        'xmlwriter_write_attribute_ns' => 1,
        'xmlwriter_write_cdata' => 1,
        'xmlwriter_write_comment' => 1,
        'xmlwriter_write_dtd' => 1,
        'xmlwriter_write_dtd_attlist' => 1,
        'xmlwriter_write_dtd_element' => 1,
        'xmlwriter_write_dtd_entity' => 1,
        'xmlwriter_write_element' => 1,
        'xmlwriter_write_element_ns' => 1,
        'xmlwriter_write_pi' => 1,
        'xmlwriter_write_raw' => 1,
        'zip_entry_open' => 2,
        'zip_open' => 0,
    );

}
