<?php
  // $Id$

require_once 'Openpear/Util.php';

OpenpearUtil::import('array_val');

class Wozozo_View {
    protected $basedir = '';
    protected $assign = array();
    protected $templateData = '';

    public function __construct($options = null) {
        $basedir = array_val($options, 'basedir');

        if (!empty($basedir)) {
            throw new Exception('basedir required');
        }
        $this->basedir = $basedir;
    }

    public function assign($arg1, $arg2 = null) {
        $vals = array();
        if (is_array($arg1)) {
            $vals = $arg1;
        } else {
            $vals = array($arg1 => $arg2);
        }
        $this->assign = array_merge($this->assign, $vals);
    }

    public function fetch($file) {
        if (!empty($this->basedir)) {
            $file = $this->basedir . $file;
        }

        if (is_file($file)) {
            extract($this->assign);

            ob_start();
            include $file;
            $contents = ob_get_clean();
            return $contents;
        }
        return false;
    }

    // special version: use template data as given string
    static public function fetchFromString($string, $assign) {
        $tmpfile = tempnam('/tmp', 'wozozo_');

        file_put_contents($tmpfile, $string);
        $view = new self;
        $view->basedir = null;
        $view->assign($assign);
        $contents = $view->fetch($tmpfile);

        unlink($tmpfile);
        return $contents;
    }
}