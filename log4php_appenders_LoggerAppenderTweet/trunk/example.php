<?php
/**
 * example log4php_appenders_LoggerAppenderTweet
 * 
 * 
 * @author yokkuns<yokkuns@tkul.jp>
 * @version 0.0.1
 */

define('LOG4PHP_CONFIGURATION', './test.properties');
define('LOG4PHP_TWITTER_ID', '******');
define('LOG4PHP_TWITTER_PASSWORD', '********');
require_once 'log4php/LoggerManager.php';

$logger = LoggerManager::getLogger('test');

$logger->debug('debug message');
$logger->info('info message');
$logger->warn('warn message');
$logger->error('error message');
$logger->fatal('fatal message');
?>