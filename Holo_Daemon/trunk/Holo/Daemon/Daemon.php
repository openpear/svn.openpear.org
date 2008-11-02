<?php


// example:
// Holo_Daemon();
class Holo_Daemon
{
    const PERFORM = 'perform';
    private $thread_num = 1;
    private $handlers = array();
    private $pid_file = '/var/run/';
 
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
        fclose(STDOUT); 
        fclose(STDIN); 
        fclose(STDERROR); 
    }

    public function run()
    {
        self::daemonize();
        pcntl_signal(SIGTERM, array($this, 'signal_handler'));
        pcntl_signal(SIGHUP, array($this, 'signal_handler'));
        while (true) {
            $this->handler(self::PERFORM);
        }
    }

   
    private function __constractor($params)
    {
        $this->thread_num = $this->ifisset($params['thread_num'], 1);
        $this->pid_file   = $tiss->ifisset($params['pid_file'], false);
        $this->log_file   = $tiss->ifisset($params['log_file'], false);
    }

    private ifisset(&$value, $default)
    {
       return isset($value) ? $value : $default;
    }


    //todo removeHandler or clearHanler
    public function addHandler($key, $function)
    {
        if (isset($this->handlers[$key]) === false) {
            $this->handlers[$key] = array();
        }
        $this->handlers[$key][] = $function;
    }

    private function signal_handler($signo)
    {
         $this->handler($signo);
         if ($signo === SIGTERM) {
              exit;
         }

    }
    
    private function handler($handler)
    {
        if (isset($this->handlers[$handler]) === true ) {
            foreach ($this->handlers[$handler] as $function) {
                $function($this);
            }
        }
    }


    private function create_pidfile() {
        if (! $this->pid_file) {
            return false;
        }

        if (! @file_put_contents($thia->pid_file, posix_getsid())) { 
            return false;
        }
        return true;
    }


    private function remove_pidfile()
    {
        if (! $this->pid_file) {
            return false;
        }

        if (file_exists($this->pid_file)) {
            return false;
        }

        unlink($this->pid_file);
        return true;
    }

    public loger($type, $message)
    {
        

    }  
}
