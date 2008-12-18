<?php

require_once 'Tumblr/Abstract.php';

class Services_Tumblr extends Services_Tumblr_Abstract
{
    const URL_READ         = "http://%s.tumblr.com/api/read";
    const URL_WRITE        = "http://www.tumblr.com/api/write";
    const URL_AUTHENTICATE = "http://www.tumblr.com/api/authenticate";

    protected $_option = array();


    public function __construct($email = null, $password = null)
    {


    }

    public function setOption($param = null, $value = null)
    {
        if (is_array($params)) {
            foreach ($params as $k => $v) {
                $this->_setOption($k, $v);
            }
        }
        else if (!is_null($param) and !is_null($value)) {
            $this->_setOption($param, $value);
        }

        return $this;
    }

    protected function _setOption($key, $value)
    {
        $this->_option[$key] = $value;

        return $this;
    }

    public function read($target_id, $options = null)
    {
        if (!is_null($options)) {
            $this->setOption($options);
        }

        $this->_sendRequest(self::API_READ);
    }

    protected function _sendRequest($method)
    {
        $rew = new HTTP_Request2();

        $call_method = "_sendRequest" . $method;

        $this->$method();

        return $this;
    }


    public function write()
    {

    }

    public function __get($prop)
    {
        $check_class_name = "Services_Tumblr_" . $prop;
        if (class_exists($check_class_name)) {
            $params = array(
                'email' => $this->_config['email'],
                'password' => $this->_config['password'],
                'options' => $this->_options,
            );

            $this->$prop = self::factory($check_class_name, $params);
            return $this->$prop;
        }
        else if (property_exists($this->data, $prop)) {
            return $this->data->getData($prop);
        }

        return null;
    }

    public function __set($prop, $value)
    {

    }

    public function __call($method, $args)
    {

    }

    public static final function factory($name, $params)
    {
        //return new $name($params['email'], $);
    }
}

