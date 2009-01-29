<?php
/**
 * Web_SessionSecurity
 *
 * @example
 *   $sess_security = Web_SessionSecurity::validate("hoge", "__foo__", 10, 600,  10);
 *   switch($sess_security->getAlert()) {
 *       case Web_SessionSecurity::DIFF_REMOTE_ADDR:
 *            echo "Remote addr is different.";break;
 *       case Web_SessionSecurity::DIFF_SECURITY_CODE:
 *            echo "Security code is different.";break;
 *   }
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 */
class Web_SessionSecurity {
    const DIFF_REMOTE_ADDR = 1;
    const DIFF_SECURITY_CODE = 2;
    private $_salt;
    private $_sess_key;

    /**
     * Constrocter
     *
     * @param $salt                  For generate security code
     * @param $sess_key              Session key
     */
    private function __construct($salt, $sess_key)
    {
        $this->_salt = $salt;
        $this->_sess_key = $sess_key;
    }

    /**
     * Validate Session
     *
     * @param $salt                  For generate security code
     * @param $sess_key              Session key
     * @param $life_sec              Life time(seconds) of security code
     * @param $leaving_sec           Leaving time(seconds)
     * @param $allow_old_hash_sec    For Web application that uses Frame
     * @return Web_SessionSecurity
     */
    static public function validate($salt, $sess_key, $life_sec, $leaving_sec, $allow_old_hash_sec)
    {
        $self = new self($salt, $sess_key);
        $self->_validate($life_sec, $leaving_sec, $allow_old_hash_sec);
        return $self;
    }

    /**
     * Get security alert
     *
     * @return int
     */
    public function getAlert()
    {
        return $this->_getSess('alert');
    }

    /**
     * Get security code
     * @return string
     */
    public function getSecurityCode()
    {
		return $this->_getSess('hash1');
    }

    /**
     * Get remote address of User
     * @return string
     */
    public function getRemoteAddr()
    {
		return $this->_getSess('remoteip');
    }

    /**
     * Get life time of Security code
     * @return int
     */
    public function getLifetime()
    {
		return $this->_getSess('lifetime');
    }

    /**
     * Get last checked time
     * @return int
     */
    public function getLastCheckedtime()
    {
		return $this->_getSess('checktime');
    }

	/**
	 * Clear security alert
	 *
	 * @return unknown_type
	 */
    public function clearAlert()
    {
        $_SESSION[$this->_sess_key] = array();
        $this->_setSess('sid', session_id());
    }

    /**
     * Validate Session
     *
     * @param $life_sec              Life time(seconds) of security code
     * @param $leaving_sec           Leaving time(seconds)
     * @param $allow_old_hash_sec    For Web application that uses Frame
     * @return boolean
     */
    private function _validate($life_sec, $leaving_sec, $allow_old_hash_sec)
    {
        // block Session Adoption
        if (session_id() != $this->_getSess('sid')) {
            $_SESSION = array();
            session_regenerate_id(true);
            $this->_setSess('sid', session_id());
        }

        // check Security alert
        if (!is_null($this->_getSess('alert'))) {
            return false;
        }

        $last = $this->_getSess('checktime');
        if (is_null($last)) {
            // init session
            $this->_updateSecurityCode();
            return true;
        }
        if ( time()-$leaving_sec > $last ) {
            if (false == $this->_eqSess('remoteip', $_SERVER['REMOTE_ADDR'])) {
                $this->_setSess('alert', self::DIFF_REMOTE_ADDR);
                return false;
            }
        }

        // update Security code
        if (!$this->_validateSecurityCode($allow_old_hash_sec)) {
            $this->_setSess('alert', self::DIFF_SECURITY_CODE);
            return false;
        }
        if ( time()-$life_sec > $this->_getSess('lifetime') ) {
            $this->_updateSecurityCode();
        }
        if ( time()-$allow_old_hash_sec > $last ) {
            $this->_setSess('checktime', time());
        }

        return true;
    }

    /**
     * Validate security code
     *
     * @param $allow_old_hash_sec    For Web application that uses Frame
     * @return boolean
     */
    private function _validateSecurityCode($allow_old_hash_sec) {
        $hash = $_COOKIE[session_name().'_HASH'];
        if ($this->_getSess('hash1') == $hash) {
            return true;
        }
        if (time() - $allow_old_hash_sec <= $this->_getSess('lifetime')) {
            if ($this->_getSess('hash2') == $hash) {
                return true;
            }
        }
        return false;
    }

    /**
     * Update security code
     *
     * @return void
     */
    private function _updateSecurityCode() {
        $old = $this->_getSess('hash1');
        $hash = md5($this->_salt . time() . session_name() . session_id());
        $this->_setSess('checktime', time());// last check time
        $this->_setSess('hash1', $hash);// security code
        $this->_setSess('hash2', $old);// old security code
        $this->_setSess('lifetime', time());// life time
        $this->_setSess('remoteip', $_SERVER['REMOTE_ADDR']);// remote ip
        setcookie(session_name().'_HASH', $hash);
    }

    /**
     *
     * @return void
     */
    private function _initSess() {
        if (!isset($_SESSION[$this->_sess_key])) {
            $_SESSION = array();
            $_SESSION[$this->_sess_key] = array();
        }
    }

    /**
     *
     * @param $key
     * @return mixed
     */
    private function _getSess($key) {
        $this->_initSess();
        return isset($_SESSION[$this->_sess_key][$key]) ? $_SESSION[$this->_sess_key][$key] : null;
    }

    /**
     *
     * @param $key
     * @param $val
     * @return void
     */
    private function _setSess($key, $val) {
        $this->_initSess();
        $_SESSION[$this->_sess_key][$key] = $val;
    }

    /**
     *
     * @param $key
     * @param $val
     * @return boolean
     */
    private function _eqSess($key, $val) {
        $this->_initSess();
        return isset($_SESSION[$this->_sess_key][$key]) ? ($_SESSION[$this->_sess_key][$key] == $val) : false;
    }
}
