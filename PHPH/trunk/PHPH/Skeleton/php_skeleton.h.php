#ifndef PHP_EXTNAME_H
#define PHP_EXTNAME_H

extern zend_module_entry extname_module_entry;
#define phpext_extname_ptr &extname_module_entry

#ifdef PHP_WIN32
#define PHP_EXTNAME_API __declspec(dllexport)
#else
#define PHP_EXTNAME_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif


#if ZEND_MODULE_API_NO < 20071006
#define zend_parse_parameters_none() \
	zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "")
#endif
#define PHP_METHOD_PASSTHRU(classname, name) \
	ZEND_MN(classname##_##name)(INTERNAL_FUNCTION_PARAM_PASSTHRU)

extern PHP_MINIT_FUNCTION(extname);
extern PHP_MSHUTDOWN_FUNCTION(extname);
extern PHP_RINIT_FUNCTION(extname);
extern PHP_RSHUTDOWN_FUNCTION(extname);
extern PHP_MINFO_FUNCTION(extname);
extern void phph_minit(INIT_FUNC_ARGS);
static void phph_register_implement(zend_class_entry *class_entry, zend_class_entry *interface_entry TSRMLS_DC);

%%PHPH_HEADER%%
#ifdef ZTS
#define EXTNAME_G(v) TSRMG(extname_globals_id, zend_extname_globals *, v)
#else
#define EXTNAME_G(v) (extname_globals.v)
#endif

#endif	/* PHP_EXTNAME_H */
