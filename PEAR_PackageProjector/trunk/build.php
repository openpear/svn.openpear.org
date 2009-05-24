<?php
ini_set('include_path', dirname(__FILE__).'/src/' . PATH_SEPARATOR . ini_get('include_path'));
require_once 'PEAR/PackageProjector.php';

/**
 * Test
 */
$project = PEAR_PackageProjector::singleton()->load(dirname(__FILE__));
$project->configure("build.conf");
//$project->checkcode();
$project->make();
//$project->updatedoc();
$project->pearinstall();
?>
