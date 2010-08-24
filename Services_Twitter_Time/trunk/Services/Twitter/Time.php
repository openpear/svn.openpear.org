<?php 

class Services_Twitter_Time {

    private $prefix = 'about ';
    private $now_time;
    private $conditions;

    public function __construct() {

        $this->now_time = time();

        $this->conditions = array (
            1         => array(
                      'range' => range(1,50),
                      'word'  => ' seconds ago'
                      ),
            60        => array(
                      'range' => range(1,59),
                      'word'  => ' minutes ago'
                      ),
            3600      => array(
                      'range' => range(1,23),
                      'word' => ' hours ago'
                      ),
            86400     => array(
                      'range' => range(1,29),
                      'word' => ' days ago'
                      ),
            1036800   => array(
                      'range' => range(1,11),
                      'word' => ' months ago'
                      ),
            12441600  => array(
                      'range' => range(1,100),
                      'word' => ' years ago'
                      ),
 
        );
    }
    public function setNowTime ($now_time) {
        $this->now_time = $now_time;
    }

    public function get ($time) {
        
        foreach ($this->conditions as $key => $conditions) {
            foreach ($conditions['range'] as $range) {
                if ($this->now_time - $time <= (int)($range * $key)) {
                    return $this->prefix . $range . $conditions['word'];
                }
            }
        }
    }
}

