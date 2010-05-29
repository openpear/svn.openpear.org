<?php

/**

Kenjiro Hirakawa <kenjiro@kenjiro.net>

*/

class Twins {

    public $template_dir;
    public $param;

    public function assign($arg1, $arg2 = NULL) {
        if (is_array($arg1)) {
            foreach ($arg1 as $key => $value) {
                $this->param[$key] = $value;
            }
        } else {
            $this->param[$arg1] = $arg2;
        }
    }

    public function fetch($template) {
        if((bool)$this->param) {
            extract($this->param);
        }
        ob_start();
        include($this->template_dir.DIRECTORY_SEPARATOR.$template);
        return ob_get_clean();
    }

}

?>
