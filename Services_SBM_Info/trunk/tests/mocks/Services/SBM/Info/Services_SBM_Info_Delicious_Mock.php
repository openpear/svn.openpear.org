<?php
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/bootstrap.php';
require_once 'Services/SBM/Info/Delicious.php';

class Services_SBM_Info_Delicious_Mock extends Services_SBM_Info_Delicious
{
    public function fetch() {
        $cache = TEST_PATH_CACHE . '/' . md5(__CLASS__ . $this->_url) . '.cache';
        if (file_exists($cache) && (time() - filemtime($cache)) < 3600) {
            return file_get_contents($cache);
        }
        $data = parent::fetch();
        if ($data) file_put_contents($cache, $data);
        return $data;
    }

    public function extractCount($data)
    {
        return parent::extractCount($data);
    }

    public function extractComments($data)
    {
        return parent::extractComments($data);
    }

    public function __set($name, $value)
    {
         return $this->{$name} = $value;
    }

    public function __get($name)
    {
         return $this->{$name};
    }
}