// $Id$
// vim:ft=javascript

// If your extension references something external, use ARG_WITH
// ARG_WITH("extname", "for extname support", "no");

// Otherwise, use ARG_ENABLE
// ARG_ENABLE("extname", "enable extname support", "no");

if (PHP_EXTNAME != "no") {
	EXTENSION("extname", "extname.c");
}
