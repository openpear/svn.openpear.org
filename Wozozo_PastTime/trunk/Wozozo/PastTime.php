<?php // -*- php; coding:utf-8 -*-
// $Id$

class Wozozo_PastTime {
    static protected $_units = array(
        array(
            'name' => '秒',
            'unit' => 1,
            ),
        array(
            'name' => '秒',
            'unit' => 10,
            'scale' => 10,
            ),
        array(
            'name' => '分',
            'unit' => 60,
            ),
        array(
            'name' => '分',
            'unit' => 600,  // 60 sec * 10min
            'scale' => 10,
            ),
        array(
            'name' => '時間',
            'unit' => 3600, // 60sec * 60min
            ),
        array(
            'name' => '日',
            'unit' => 86400, // 60sec * 60min * 24hour
            ),
        array(
            'name' => '月',
            'unit' => 2592000, // 60sec * 60min * 24hour * 30day
            ),
        array(
            'name' => '年',
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
        
        return $num . $unitname . '前';
    }

    static public function past($time, $now = null) {
        if ($now === null) {
            $now = time();
        }
        return self::pastString($now - $time);
    }
}

?>
