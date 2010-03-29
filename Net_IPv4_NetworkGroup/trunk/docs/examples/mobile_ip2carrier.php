<?php
require_once 'Net/IPv4/NetworkGroup.php';
//require_once dirname(__FILE__) . '/../../NetworkGroup.php';

if($argc<2) {
  echo 'Usage: ' . $_SERVER['SCRIPT_NAME'] . ' <ip-address>' . PHP_EOL;
  exit;
}

$group = new Net_IPv4_NetworkGroup(array('config'=>dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mobile_ips.yml'));
echo $group->getGroup($argv[1], 'pc') . PHP_EOL;
?>
