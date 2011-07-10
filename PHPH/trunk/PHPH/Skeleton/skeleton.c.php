#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_extname.h"

/* True global resources - no need for thread safety here */
static int le_extname;

/* PHP_INI
 */
/* Remove comments and fill if you need to have entries in php.ini
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("extname.global_value",      "42", PHP_INI_ALL, OnUpdateLong, global_value, zend_extname_globals, extname_globals)
    STD_PHP_INI_ENTRY("extname.global_string", "foobar", PHP_INI_ALL, OnUpdateString, global_string, zend_extname_globals, extname_globals)
PHP_INI_END()
*/

/* php_extname_init_globals
 */
/* Uncomment this function if you have INI entries
static void php_extname_init_globals(zend_extname_globals *extname_globals)
{
	extname_globals->global_value = 0;
	extname_globals->global_string = NULL;
}
*/

/* PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(extname)
{
	/* If you have INI entries, uncomment these lines 
	REGISTER_INI_ENTRIES();
	*/
	phph_minit(INIT_FUNC_ARGS_PASSTHRU);
	return SUCCESS;
}

/* PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(extname)
{
	/* uncomment this line if you have INI entries
	UNREGISTER_INI_ENTRIES();
	*/
	return SUCCESS;
}

/* Remove if there's nothing to do at request start */
/* PHP_RINIT_FUNCTION
 */
PHP_RINIT_FUNCTION(extname)
{
	return SUCCESS;
}

/* Remove if there's nothing to do at request end */
/* PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(extname)
{
	return SUCCESS;
}

/* PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(extname)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "extname support", "enabled");
	php_info_print_table_end();

	/* Remove comments if you have entries in php.ini
	DISPLAY_INI_ENTRIES();
	*/
}


%%PHPH_PHP_FUNCTION%%
