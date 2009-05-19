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
            'desc'  => 'project directory');
$config['configure'] = array(
            'short' => 'confg',
            'min'   => 0,
            'max'   => -1,
            'desc'  => 'clear .pearproject and configure package');
$config['make'] = array(
            'max'   => 0,
            'desc'  => 'make package');
$config['create'] = array(
            'max'   => 0,
            'desc'  => 'create project directory.');
$config['clear'] = array(
            'short' => 'c',
            'max'   => 0,
            'desc'  => 'clear .pearproject');
$config['tmp'] = array(
            'short' => 't',
            'max'   => 0,
            'desc'  => 'use temporary directory');
$config['checkcode'] = array(
            'max'   => 0,
            'desc'  => 'source of project is checked by CodeSniffer.');

$args =& Console_Getargs::factory($config, $argv);
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
	    $project = PEAR_PackageProjector::singleton()->create($projectpath, $args->isDefined('tmp'));
	} else {
	    $project = PEAR_PackageProjector::singleton()->load($projectpath, $args->isDefined('tmp'));
	}

	/**
	 * clear
	 */
	if ($args->isDefined('clear')) {
	    $project->clear();
	}

	/**
	 * checkcode
	 */
	if ($args->isDefined('checkcode')) {
	    $project->checkcode();
	}

	/**

	 * configure
	 */
	if ($args->isDefined('configure')) {
	    $confpath = $args->getValue('configure');
	    $project->configure($confpath);
	}

	/**
	 * make
	 */
	if ($args->isDefined('make')) {
	    $project->make();
	}
} catch (Exception $e) {
	echo $e->getMessage();
}
?>
