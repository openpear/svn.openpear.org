<?php
require dirname(__FILE__) . '/../Holo/Daemon.php';
$pid_file = dirname(__FILE__) . '/sample.pid';

$daemon = new Holo_Daemon(array('pid_file' => $pid_file));
$daemon->setHandler(Holo_Daemon::PERFORM, 'sample');
function sample() {
    $log_file = dirname(__FILE__) . '/log.data';
    for ($i = 0; $i < 100; $i++) {
        error_log(sprintf("count:%s\n", $i), 3, $log_file);
        sleep(1);
    }
}
$cmd = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
switch ($cmd) {
    case 'stop':
      $daemon->stop();
      break;
    case 'start':
      $daemon->start();
      break;
} 
