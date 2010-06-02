<?php
require_once 'Net/IRC/Pattern.php';

class Net_IRC_Message
{
    protected $prefix;
    protected $command;
    protected $params;

    protected $nick;
    protected $channel;
    protected $message;

    public function __construct($line) {
        if (preg_match(Net_IRC_Pattern::message_pattern(), $line, $match)) {
            $_ = array_shift($match);
            if (preg_match('/^(.+?)!/', $_, $nick)) {
                $this->nick = $nick[1];
            }
            $prefix = array_shift($match);
            $command = array_shift($match);

            if (isset($match[0]) && !empty($match[0])) {
                list($middle, $trailer) = $match;
            } else if (isset($match[2]) && !empty($match[2])) {
                list($middle, $trailer) = array_slice($match, 2, 2);
            } else if (isset($match[1])) {
                $params = array();
                $trailer = $match[1];
            } else if (isset($match[3])) {
                $params = array();
                $trailer = $match[3];
            } else {
                $params = array();
            }
            
            if (!isset($params)) {
                $params = explode(' ', trim($middle));
            }
            if (!empty($trailer)) {
                $params = array_merge($params, array($trailer));
            }

            $this->prefix = $prefix;
            $this->command = $command;
            $this->params = $params;
            return ;
        }
        throw new Net_IRC_Exception('invalid message');
    }

    public function __call($method, $args) {
        if (empty($args) && isset($this->$method)) {
            return $this->$method;
        }
    }
}
