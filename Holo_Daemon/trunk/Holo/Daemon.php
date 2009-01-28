<?php


// example:
// Holo_Daemon();
class Holo_Daemon
{
    const PERFORM = 'perform';

    private $handlers = array();
    private $pid_file = '/var/run/holo_daemon.pid';
 
    static public function daemonize()
    { 
        declare(ticks = 1);
        ini_set("max_execution_time", "0");
        ini_set("max_input_time", "0");
        set_time_limit(0);

        $pid = pcntl_fork();
        if($pid) {
            exit(); 
        }
     
        posix_setsid();
     
        $pid = pcntl_fork();
        if($pid) {
            exit(); 
        }

        chdir('/');
        umask(0);
        //fclose(STDOUT); 
        //fclose(STDIN); 
        //fclose(STDERR); 
    }

    public function start()
    {
        self::daemonize();
        $this->create_pidfile();
        pcntl_signal(SIGTERM, array($this, 'signal_handler'));
        pcntl_signal(SIGHUP, array($this, 'signal_handler'));
        $this->handler(self::PERFORM);
        $this->remove_pidfile();
    }

    public function stop()
    {
        $pid = $this->read_pidfile();
        if ($pid !== false) {
            posix_kill($pid, SIGTERM);
        }
    }

    public function __construct($params)
    {
        $this->pid_file   = $this->ifisset($params['pid_file'], false);
    }

    private function ifisset(&$value, $default)
    {
       return isset($value) ? $value : $default;
    }

    public function setHandler($key, $function)
    {
        $this->handlers[$key] = $function;
    }

    protected function signal_handler($signo)
    {
    var_dump($signo);
        $this->handler($signo);
        if ($signo === SIGTERM) {
            $this->remove_pidfile();
            exit;
        }
    }
    
    private function handler($handler)
    {
        if (isset($this->handlers[$handler]) === true ) {
            call_user_func($this->handlers[$handler]);
        }
    }


    private function create_pidfile() {
        if (! @file_put_contents($this->pid_file, posix_getpid())) { 
            return false;
        }
        return true;
    }


    private function remove_pidfile()
    {
        if (! file_exists($this->pid_file)) {
            return false;
        }

        unlink($this->pid_file);
        return true;
    }


    private function read_pidfile()
    {
        if (! file_exists($this->pid_file)) {
            return false;
        }

        return file_get_contents($this->pid_file);
    }
}
