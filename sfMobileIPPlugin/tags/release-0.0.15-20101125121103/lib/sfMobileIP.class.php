<?php
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2: */

class sfMobileIP
{
  static public function carrier($addr = null)
  {
    if (!$addr) {
      $addr = self::addr();
    }

    $ip = (double)(sprintf('%u', ip2long($addr)));
    $ranges = self::ranges();
    $min = -1;
    $max = count($ranges);
    while (true) {
      $center = (int)floor(($min+$max)/2);
      if ($center === $min) {
        return 'pc';
      }

      $range =& $ranges[$center];

      if ($ip < $range['network']) {
        $max = $center;
      } else if ($range['network'] <= $ip && $ip <= $range['broadcast']) {
        return $range['carrier'];
      } else {
        $min = $center;
      }
    }
  }

  static protected function addr()
  {
    if (isset($_SERVER['REMOTE_ADDR'])) {
      return $_SERVER['REMOTE_ADDR'];
    }

    return '127.0.0.1';
  }

  static protected function configFile()
  {
    $config_dir  = sfConfig::get('sf_config_dir_name');
    $config_file = $config_dir.DIRECTORY_SEPARATOR.'mobile_ips.yml';
    return sfConfigCache::getInstance()->checkConfig($config_file);
  }

  static protected function ranges()
  {
    static $ranges = null;
    if ($ranges) {
      return $ranges;
    }

    $ranges = require_once(self::configFile());
    return $ranges;
  }
}
