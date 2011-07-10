#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_extname.h"

/* ce_xxx
 * 
 * zend_class_entry
 */
%%PHPH_CLASS_ENTRY%%

/* arg_info
 */
%%PHPH_ARG_INFO%%

/* extname_functions[]
 *
 * Every user visible function must have an entry in extname_functions[].
 */
zend_function_entry extname_functions[] = {
%%PHPH_FUNCTION_ENTRY%%
	{NULL, NULL, NULL}	/* Must be the last line in extname_functions[] */
};

/* xxx_methods[]
 *
 * zend_function_entry
 */
%%PHPH_METHOD_ENTRY%%

/* extname_module_entry
 */
zend_module_entry extname_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"extname",
	extname_functions,
	PHP_MINIT(extname),
	PHP_MSHUTDOWN(extname),
	PHP_RINIT(extname),		/* Replace with NULL if there's nothing to do at request start */
	PHP_RSHUTDOWN(extname),	/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(extname),
#if ZEND_MODULE_API_NO >= 20010901
	"0.1", /* Replace with version number for your extension */
#endif
	STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_EXTNAME
ZEND_GET_MODULE(extname)
#endif

void phph_minit(INIT_FUNC_ARGS)
{
	zend_class_entry ce;
	zend_class_entry *pce;
	zend_class_entry *ice;

%%PHPH_MINIT%%
}

static void phph_register_implement(zend_class_entry *class_entry, zend_class_entry *interface_entry TSRMLS_DC)
{
	zend_uint num_interfaces = ++class_entry->num_interfaces;

	class_entry->interfaces = (zend_class_entry **) realloc(class_entry->interfaces, sizeof(zend_class_entry *) * num_interfaces);
	class_entry->interfaces[num_interfaces - 1] = interface_entry;
}
