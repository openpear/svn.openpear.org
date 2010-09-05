<?php
require_once 'PHPUnit/Framework.php';

define('TEST_PATH_CACHE', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'caches');

// Target
define('TEST_SBM_INFO_URL',   'http://example.com/');
define('TEST_SBM_INFO_TITLE', 'Example Web Page');

// Set include path
set_include_path(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'src');
foreach (array('cases', 'mocks') as $path) {
    set_include_path(dirname(__FILE__) . DIRECTORY_SEPARATOR . $path . PATH_SEPARATOR . get_include_path());
}
