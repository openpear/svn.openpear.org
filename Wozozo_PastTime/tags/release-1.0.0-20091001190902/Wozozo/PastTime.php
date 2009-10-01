<?php // -*- php; coding:utf-8 -*-
// $Id$

class Wozozo_PastTime {
    static protected $_units = array(
        array(
            'name' => '$BIC(B',
            'unit' => 1,
            ),
        array(
            'name' => '$BIC(B',
            'unit' => 10,
            'scale' => 10,
            ),
        array(
            'name' => '$BJ,(B',
            'unit' => 60,
            ),
        array(
            'name' => '$BJ,(B',
            'unit' => 600,  // 60 sec * 10min
            'scale' => 10,
            ),
        array(
            'name' => '$B;~4V(B',
            'unit' => 3600, // 60sec * 60min
            ),
        array(
            'name' => '$BF|(B',
            'unit' => 86400, // 60sec * 60min * 24hour
            ),
        array(
            'name' => '$B7n(B',
            'unit' => 2592000, // 60sec * 60min * 24hour * 30day
            ),
        array(
            'name' => '$BG/(B',
            'unit' => 31536000, // 60sec * 60min * 24hour * 365day
            ),
        );
    
    static public function pastString($sec) {
        if ($sec < 0) {
            $sec = 1;
        }
        $num = null;
        foreach (self::$_units as $param) {
            $unit = $param['unit'];
            if ($sec < $unit) {
                $num = ceil($sec / $prev_param['unit']);
                if (isset($prev_param['scale'])) {
                    $num *= $prev_param['scale'];
                }
                $unitname = $prev_param['name'];
                break;
            }
            $prev_param = $param;
        }

        if (empty($num)) {
            $num = ceil($sec / $prev_param['unit']);
            if (isset($prev_param['scale'])) {
                $num *= $prev_param['scale'];
            }
            $unitname = $prev_param['name'];
        }
        
        return $num . $unitname . '$BA0(B';
    }

    static public function past($time, $now = null) {
        if ($now === null) {
            $now = time();
        }
        return self::pastString($now - $time);
    }
}

?>
