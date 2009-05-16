<?php
ini_set('include_path', dirname(__FILE__).'/src/' . PATH_SEPARATOR . ini_get('include_path'));
require_once 'PEAR/PackageProjector.php';

/**
 * Test
 */
$project = PEAR_PackageProjector::singleton()->load(dirname(__FILE__));
//   HACK for PEAR 1.8.0.
//   When invoke checkcode() method, PEAR_PackageFileManager2#addReplacement
//   does not work because of invocation of PHP_CodeSniffer, which changes current
//   working directory.
//$project->checkcode();
$project->configure(dirname(__FILE__)."/build.conf");
$project->make();
?>
