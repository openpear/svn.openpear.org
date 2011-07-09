dnl $Id$
dnl config.m4 for extension extname

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

dnl PHP_ARG_WITH(extname, for extname support,
dnl Make sure that the comment is aligned:
dnl [  --with-extname             Include extname support])

dnl Otherwise use enable:

PHP_ARG_ENABLE(extname, whether to enable extname support,
Make sure that the comment is aligned:
[  --enable-extname           Enable extname support])

if test "$PHP_EXTNAME" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-extname -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/extname.h"  # you most likely want to change this
  dnl if test -r $PHP_EXTNAME/$SEARCH_FOR; then # path given as parameter
  dnl   EXTNAME_DIR=$PHP_EXTNAME
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for extname files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       EXTNAME_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$EXTNAME_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the extname distribution])
  dnl fi

  dnl # --with-extname -> add include path
  dnl PHP_ADD_INCLUDE($EXTNAME_DIR/include)

  dnl # --with-extname -> check for lib and symbol presence
  dnl LIBNAME=extname # you may want to change this
  dnl LIBSYMBOL=extname # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $EXTNAME_DIR/lib, EXTNAME_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_EXTNAMELIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong extname lib version or lib not found])
  dnl ],[
  dnl   -L$EXTNAME_DIR/lib -lm -ldl
  dnl ])
  dnl
  dnl PHP_SUBST(EXTNAME_SHARED_LIBADD)

  PHP_NEW_EXTENSION(extname, php_extname.c extname.c, $ext_shared)
fi
