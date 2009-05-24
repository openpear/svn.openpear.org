#!@php_bin@
<?php
/**
 * @package PEAR_PackageProjector
 * @author  Kouichi Sakamoto
 */

//error_reporting(E_ALL | E_STRICT);
require_once 'PEAR/PackageProjector.php';
require_once 'Console/Getargs.php';

$config = array();
$config['project'] = array(
            'short' => 'p',
            'min'   => 1,
            'max'   => 1,
            'default'=>'./',
            'desc'  => 'project directory');
$config['configure'] = array(
            'short' => 'confg',
            'min'   => 1,
            'max'   => 1,
            'default'=>'build.conf',
            'desc'  => 'setting configure package');
$config['make'] = array(
            'max'   => 0,
            'desc'  => 'make package');
$config['create'] = array(
            'min'   => 0,
            'max'   => 1,
            'default'=>'',
            'desc'  => 'create project directory.');
$config['clear'] = array(
            'short' => 'c',
            'max'   => 0,
            'desc'  => 'clear .pearproject.Now not use from ver1.0.0.');
$config['tmp'] = array(
            'short' => 't',
            'max'   => 0,
            'desc'  => 'use temporary directory.Now not use from ver1.0.0.');
$config['checkcode'] = array(
            'short' => 'check',
            'max'   => 0,
            'desc'  => 'source of project is checked by CodeSniffer.');
$config['updatedoc'] = array(
            'short' => 'doc',
            'max'   => 0,
            'desc'  => 'update or create document.');
$config['install'] = array(
            'short' => 'i',
            'min'   => 0,
            'max'   => 1,
            'default'=>'',
            'desc'  => 'pear install');

$args =& Console_Getargs::factory($config, $argv);
if (2>count($argv)) {
    echo Console_Getargs::getHelp($config)."\n";
    exit;
}
if (PEAR::isError($args)) {
    if ($args->getCode() === CONSOLE_GETARGS_ERROR_USER) {
        echo Console_Getargs::getHelp($config, null, $args->getMessage())."\n";
    } else if ($args->getCode() === CONSOLE_GETARGS_HELP) {
        echo Console_Getargs::getHelp($config)."\n";
    }
    exit;
}

$projectpath = $args->getValue('project');
$project = null;

try {
	if ($args->isDefined('create')) {
		$create_proj = $args->getValue('create');
		$create_proj = '' === $create_proj ? $projectpath : $create_proj;
	    $project = PEAR_PackageProjector::singleton()->create($create_proj);
	} else {
	    $project = PEAR_PackageProjector::singleton()->load($projectpath);
	}

	/**
	 * clear
	if ($args->isDefined('clear')) {
	    $project->clear();
	}
	 */

	/**

	 * configure
	 */
	if ($args->isDefined('configure') && !$args->isDefined('create')) {
	    $confpath = $args->getValue('configure');
	    $project->configure($confpath);
	}

	/**
	 * checkcode
	 */
	if ($args->isDefined('checkcode')) {
	    $project->checkcode();
	}

	/**
	 * make
	 */
	if ($args->isDefined('make')) {
	    $project->make();
	}

	/**
	 * updatedoc
	 */
	if ($args->isDefined('updatedoc')) {
	    $project->updatedoc();
	}

	/**
	 * pearinstall
	 */
	if ($args->isDefined('install')) {
        $version = $args->getValue('install');
	    $project->pearinstall($version);
	}
} catch (Exception $e) {
	echo $e->getMessage();
}
?>
