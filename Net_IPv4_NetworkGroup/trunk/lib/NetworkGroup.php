<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * This file is part of Net_IPv4_NetworkGroup library.
 *
 * PHP version 5.2
 *
 * @category Networking
 * @package Net_IPv4_NetworkGroup
 * @link http://openper.org/package/Net_IPv4_NetworkGroup
 * @license MIT
 * @author MugeSo <mugeso@mugeso.com>
 */

require_once "Cache/Lite.php";

/**
 * This class manages ipv4 network group.
 *
 * @since 0.1.0
 * @author MugeSo <mugeso@mugeso.com>
 */
class Net_IPv4_NetworkGroup
{
  protected $options;

  /**
   * the constructor
   * 
   * @param array $options
   *  cache_dir - directory path for caching
   *  config    - yaml configuration file.
   */
  public function __construct(array $options = array()) {
    $options += array(
        'cache_dir'=>sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'Net_IPv4_NetworkGroup' . DIRECTORY_SEPARATOR,
        'config'=>null
    );

    if (empty($options['config'])) {
      throw new InvalidArgumentException('config parameter for configuration file required.');
    }

    $this->options = $options;
  }

  /**
   *
   * @param string $addr
   * @param mixed $default
   * @return string
   */
  public function getGroup($addr = null, $default = null)
  {
    if (!$addr) {
      $addr = self::addr();
    }

    $ip = (double)(sprintf('%u', ip2long($addr)));
    $ranges = $this->getRanges();

    $min = -1;
    $max = count($ranges);
    while (true) {
      $center = (int)floor(($min+$max)/2);
      if ($center === $min) {
        return $default;
      }

      $range =& $ranges[$center];

      if ($ip < $range['network']) {
        $max = $center;
      } else if ($range['network'] <= $ip && $ip <= $range['broadcast']) {
        return $range['group'];
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
  
  protected function getRanges()
  {
    $options = $this->options;
    $cache = new Cache_Lite(array('cacheDir'=>$options['cache_dir'], 'lifeTime'=>null, 'automaticSerialization'=>true));

    $ranges = $cache->get($options['config']);

    if ($ranges===false || $cache->lastModified() < filemtime($options['config'])) {
      // is not cached. Let's complie the config.
      $ranges = $this->compileConfig($options['config']);
      $cache->save($ranges);
    }

    return $ranges;
  }

  public function compileConfig($configFile)
  {
    class_exists('Net_IPv4') or require_once 'Net/IPv4.php';
    
    $config = $this->parseYaml($configFile);

    // get all ip ranges
    $ranges = array();
    foreach ($config as $group => $group_ips) {
      foreach ($group_ips as $cidr) {
        $cidr = @Net_IPv4::parseAddress($cidr);
        $ranges[] = array(
            'broadcast' => $this->ip2double($cidr->broadcast),
            'group'   => $group,
            'network'   => $this->ip2double($cidr->network));
      }
    }

    // sort
    usort($ranges, array($this, 'compareByNetwork'));

    // unite sequent ip range
    $l = count($ranges);
    $cur =& $ranges[0];
    for ($i = 1; $i < $l; $i++) {
      $next =& $ranges[$i];
      $condition = ($cur['broadcast']+1) === $next['network'] &&
          $cur['group'] === $next['group'];
      if ($condition) {
        $cur['broadcast'] = $next['broadcast'];
        unset($ranges[$i]);
      } else {
        $cur =& $ranges[$i];
      }
    }
    $ranges = array_values($ranges);

    return $ranges;
  }

  protected function compareByNetwork($a, $b)
  {
    if ($a['network'] === $b['network']) {
      return 0;
    } else if ($a['network'] < $b['network']) {
      return -1;
    } else {
      return 1;
    }
  }

  protected function ip2double($addr)
  {
    return (double)(sprintf('%u', ip2long($addr)));
  }

  protected function parseYaml($yamlFile)
  {
    if(class_exists('Spyc')) {
      // noting todo.
    } elseif(@fclose(@fopen('spyc.php', 'r', true))) {
      require_once 'spyc.php';
    } else {
      throw new RuntimeException('Net_IPv4_NetworkGroup requries Spyc. see http://code.google.com/p/spyc/');
    }

    return Spyc::YAMLLoad($yamlFile);
  }
}
?>
