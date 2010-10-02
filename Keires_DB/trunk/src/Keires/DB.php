<?php
/**
  * Keires_DB
  *
  * Database utility classes
  *
  * PHP version 5
  *
  * LICENSE: This source file is subject to version 3.0 of the PHP license
  * that is available through the world-wide-web at the following URI:
  * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
  * the PHP License and are unable to obtain it through the web, please
  * send a note to license@php.net so we can mail you a copy immediately.
  *
  * @category   DB
  * @package    Keires_DB
  * @author     KOYAMA Tetsuji <koyama@hoge.org>
  * @copyright  2010 KOYAMA Tetsuji
  * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
  * @version    svn: $Id$
  * @link       http://openpear.org/package/Keires_DB
  * @since      File available since Release 0.1
  */

require_once 'Openpear/Util.php';

Openpear_Util::import('array_val');

/*
 *  Keires_DB_Exception
 */

class Keires_DB_Exception extends Exception {
    const INVALID_PARAM = 10;
    const ALREADY_INITIALIZED = 11;
    const UNKNOWN_METHOD = 12;
}

/*
 *  Keires_DB_Ext
 */

class Keires_DB_Ext {
    private $_db = null;

    protected $_fetchMode = PDO::FETCH_ASSOC;

    public function __construct($db) {
        $this->_db = $db;
    }

    public static function applyLimitStmt(&$sql, $offset, $limit) {
        if ($limit > 0) {
            $sql .= " LIMIT ".$limit;
        }
        if ($offset > 0) {
            $sql .= " OFFSET ".$offset;
        }
    }

    public function __call($method, $params) {
        if (is_callable(array($this->_db, $method))) {
            return call_user_func_array(array($this->_db, $method),
                                        $params);
        }
        throw new Keires_DB_Exception('Undefined method call');
    }

    public function applyLimit(&$sql, $offset, $limit) {
        self::applyLimitStmt($sql, $offset, $limit);
    }

    // addition methods
    
    public function execute($sql, $params = array()) {
        $stmt = $this->_db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getOne($sql, $params = array()) {
        $stmt = $this->_db->prepare($sql);
        if (!$stmt->execute($params)) {
            return null;
        }
        $row = $stmt->fetch(PDO::FETCH_NUM);
        if ($row === false) {
            return null;
        }
        return $row[0];
    }

    public function getRow($sql, $params = array()) {
        $stmt = $this->_db->prepare($sql);
        if (!$stmt->execute($params)) {
            return null;
        }
        $stmt->setFetchMode($this->_fetchMode);
        return $stmt->fetch();
    }

    public function getAll($sql, $params = array()) {
        $stmt = $this->_db->prepare($sql);
        if (!$stmt->execute($params)) {
            return null;
        }
        $stmt->setFetchMode($this->_fetchMode);
        return $stmt->fetchAll();
    }

    // manipulate SEQUENCE

    public function nextval($seqname) {
        $sql = 'SELECT nextval(?)';
        return $this->getOne($sql, array($seqname));
    }

    public function existsSequence($seqname) {
        // for PostgreSQL only
        $sql = 'SELECT COUNT(*) FROM pg_catalog.pg_class'.
            ' WHERE relkind=? AND relname=?';
        $c = $this->getOne($sql, array('S', $seqname));
        return ($c != 0);
    }

    public function createSequence($seqname) {
        $sql = 'CREATE SEQUENCE '. $seqname;
        return $this->execute($sql);
    }

    public function dropSequence($seqname) {
        $sql = 'DROP SEQUENCE ' . $seqname;
        return $this->execupte($sql);
    }
}

/*
 * Keires_DB
 */

class Keires_DB {
    const DB_ENCODING = 'UTF-8';

    const DEFAULT_DB_TYPE = 'default';

    static public $db_type = self::DEFAULT_DB_TYPE;
    static protected $db_type_stack = array();

    static protected $_dsn = array();

    private $_db = array();

    static public function init($options = null) {
        // check initialize past
        if (!empty(self::$_dsn)) {
            throw new Keires_DB_Exception(
                'Already initialized',
                Keires_DB_Exception::ALREADY_INITIALIZED);
        }

        $types = array_val($options, 'types');

        if (empty($types)) {
            throw new Keires_DB_Exception(
                '"types" parameter is needed.',
                Keires_DB_Exception::INVALID_PARAM);
        }

        $dsns = array();
        foreach ($types as $db_type => $param) {
            $dsn = array_val($param, 'dsn');
            if (empty($dsn)) {
                throw new Keires_DB_Exception(
                    'empty DSN in db_type: ' . $db_type,
                    Keires_DB_Exception::INVALID_PARAM);
            }
            $dsns[$db_type] = $dsn;
        }
        if (empty($dsns)) {
            throw new Keires_DB_Exception(
                'Empty DSN',
                Keires_DB_Exception::INVALID_PARAM);
        }
        self::$_dsn = $dsns;
    }

    private function __construct() {
    }

    protected function getConnection($type = null) {
        if (empty($type)) {
            $type = self::$db_type;
        }
        if (isset($this->_db[$type])) {
            // already connected
            return $this->_db[$type];
        }

        if (!isset(self::$_dsn[$type])) {
            throw new Keires_DB_Exception(
                'DSN is not decrared with type:' . $type,
                Keires_DB_Exception::INVALID_PARAM);
        }

        $dsn = self::$_dsn[$type];

        $db = new PDO($dsn);
        $this->_db[$type] = new Keires_DB_Ext($db);
        return $this->_db[$type];
    }

    // push/pop DB type setting

    static public function pushDbType($type) {
        $cur = self::$db_type;
        array_push(self::$db_type_stack, $cur);
        self::$db_type = $type;
    }

    static public function popDbType() {
        $saved = array_pop(self::$db_type_stack);
        if ($saved !== null) {
            self::$db_type = $saved;
        }
    }

    static public function getDB($type = null) {
        $_this = self::getInstance();
        return $_this->getConnection($type);
    }

    // utility methods to insert/update

    static public function checkRequired($data, $keys) {
        if (is_string($keys)) {
            $keys = array($keys);
        }
        foreach ($keys as $key) {
            if (!isset($data[$key]) || empty($data[$key])) {
                return false;
            }
        }
        return true;
    }

    static public function buildKeyVal($params, $valid_keys) {
        $keys = array();
        $vals = array();

        foreach ($params as $key => $val) {
            if (in_array($key, $valid_keys)) {
                $keys[] = $key;
                $vals[] = $val;
            }
        }
        return array($keys, $vals);
    }

    static public function genCondition($keys) {
        $cond = '';
        foreach ($keys as $key) {
            if (!empty($cond)) {
                $cond .= ' AND ';
            }
            $cond .= $key . '=?';
        }
        return $cond;
    }
    
    static public function genInsertSQL($table, $keys) {
        $ks = '';
        $values = '';
        foreach ($keys as $key) {
            if (!empty($ks)) {
                $ks .= ',';
                $values .= ',';
            }
            $ks .= $key;
            $values .= '?';
        }
        return 'INSERT INTO ' . $table .
            ' (' . $ks . ') VALUES (' . $values . ')';
    }
    
    static public function genUpdateSQL($table, $keys) {
        $values = '';
        foreach ($keys as $key) {
            if (!empty($values)) {
                $values .= ',';
            }
            $values .= $key . '=?';
        }
        return 'UPDATE ' . $table . ' SET ' . $values;
    }

    // type conversion

    static public function adjustType(&$val, $type) {
        switch ($type) {
        case 'integer':
            $val = (integer)$val;
            break;
        case 'float':
            $val = (float)$val;
            break;
        case 'boolean':
            if (!is_bool($val)) {
                if (is_string($val)) {
                    $val = self::pgbool($val);
                } else if (is_numeric($val)) {
                    $val = ($val == 0)? false: true;
                } else {
                    $val = (bool)$val;
                }
            }
            // PDO required string 'true' or 'false'
            $val = ($val)? 'true': 'false';
            break;
        case 'date':
        case 'timestamp':
            if ($val === '') {
                $val = null;
            }
            break;
        default: // string or text
            $val = (string)$val;
            break;
        }
        return $val;
    }

    static public function convType(&$vals, $keys, $type) {
        foreach ($keys as $key) {
            if (isset($vals[$key])) {
                $vals[$key] = self::adjustType($vals[$key], $type);
            }
        }
    }

    static public function convTypeAll(&$vals, $keytypes) {
        foreach ($vals as $key => &$val) {
            if (isset($keytypes[$key])) {
                $type = $keytypes[$key];
                self::adjustType($val, $type);
            }
        }
    }

    // abstruct table utils

    static public function getTypeInfo($tuppleInfo) {
        $typeInfo = array();
        foreach ($tuppleInfo as $name => $info) {
            if (isset($info['type'])) {
                $typeInfo[$name] = $info['type'];
            }
        }
        return $typeInfo;
    }

    static public function addTuppleName(&$tuppleInfo, $names) {
        foreach ($names as $tupple => $name) {
            if (isset($tuppleInfo[$tupple])) {
                $tuppleInfo[$tupple]['name'] = $name;
            }
        }
    }

    static public function getValidKeys($tuppleInfo) {
        $valid_keys = array();
        foreach ($tuppleInfo as $name => $info) {
            if (isset($info['ignore']) || isset($info['auto_value'])) {
                continue;
            }
            $valid_keys[] = $name;
        }
        return $valid_keys;
    }

    // valid literal

    static public function safeLiteralString($name) {
        return preg_replace('/[^a-z0-9_.]/i', '_', $name);
    }

    // utility

    static public function pgbool($val) {
        $result = false;
        if ((strcasecmp($val, 'true') == 0) ||
            (strcasecmp($val, 't') == 0)) { // for pgsql
            $result = true;
        }
        return $result;
    }

    // Singleton Interface

    private static $_singleton = null;

    static public function getInstance() {
        if (self::$_singleton == null) {
            self::$_singleton = new self;
        }
        return self::$_singleton;
    }
}

/*
 *  Keires_DB_Impl
 */

class Keires_DB_Impl {
    protected $db_type = null;
    protected $table_name;
    protected $tupple_info;

    public function __construct($options) {
        $db_type = array_val($options, 'db_type');
        $table_name = array_val($options, 'table_name');
        $tupple_info = array_val($options, 'tupple_info');

        if (empty($table_name) || empty($tupple_info)) {
            throw new Keires_DB_Exception(
                'table_name, tupple_info are needed',
                Keires_DB_Exception::INVALID_PARAM);
        }
        
        $this->db_type = $db_type;
        $this->table_name = $table_name;
        $this->tupple_info = $tupple_info;
    }

    public function getDB() {
        return Keires_DB::getDB($this->db_type);
    }

    public function tuppleInfo($name) {
        if (isset($this->tupple_info[$name])) {
            return $this->tupple_info[$name];
        }
        return null;
    }

    public function tuppleType($name) {
        if (isset($this->tupple_info[$name]) &&
            isset($this->tupple_info[$name]['type'])) {
            return $this->tupple_info[$name]['type'];
        }
        return null;
    }

    public function tuppleTitles() {
        $titles = array();
        foreach ($this->tupple_info as $tupple => $info) {
            $title = array_val($info, 'title');
            if (!empty($title)) {
                $titles[] = array(
                    'tupple' => $tupple,
                    'title' => $title,
                    );
            }
        }
        return $titles;
    }

    public function getPrimaries() {
        $result = array();
        foreach ($this->tupple_info as $name => $info) {
            if (isset($info['primary']) && ($info['primary'] === true)) {
                $result[] = $name;
            }
        }
        return $result;
    }

    public function autoValues($when) {
        $result = array();
        if (($when != 'insert') && ($when != 'update')) {
            return $result;
        }
        foreach ($this->tupple_info as $name => $info) {
            if (!isset($info['auto_value'])) {
                continue;
            }
            $av = $info['auto_value'];
            if (!is_array($av)) {
                continue;
            }

            if (isset($av[$when]) && $av[$when]) {
                // NOTICE: eval() called
                $result[$name] = eval('return ' . $av['value'] . ';');
            }
        }
        return $result;
    }

    public function makePrimaryCondition() {
        $tupples = $this->getPrimaries();
        $condition = '';
        foreach ($tupples as $tupple) {
            if (!empty($condition)) {
                $condition .= ' AND ';
            }
            $condition .= $tupple . '=?';
        }
        return $condition;
    }

    protected function checkPrimaryArgs(&$args) {
        $primary = $this->getPrimaries();
        
        if (count($args) != count($primary)) {
            throw new Keires_DB_Exception(
                'Wrong prarameters count of primary key',
                Keires_DB_Exception::INVALID_PARAM);
        }
        $count = count($args);
        for ($i = 0; $i < $count; ++$i) {
            $name = $primary[$i];
            $type = $this->tuppleType($name);
            Keires_DB::adjustType($args[$i], $type);
        }
    }

    // operation
    
    public function exists() {
        $args = func_get_args();
        $this->checkPrimaryArgs($args);

        if (count($args) == 0) {
            return false;
        }

        $sql = 'SELECT COUNT(*) FROM ' . $this->table_name .
            ' WHERE ' . $this->makePrimaryCondition();
        $db = $this->getDB();
        $c = $db->getOne($sql, $args);
        return ($c != 0);
    }

    public function getInfo() {
        $args = func_get_args();
        $this->checkPrimaryArgs($args);

        if (count($args) == 0) {
            return array();
        }

        $sql = 'SELECT * FROM ' . $this->table_name .
            ' WHERE ' . $this->makePrimaryCondition();
        $db = $this->getDB();
        return $db->getRow($sql, $args);
    }

    public function getPrimaryKeyVals($data) {
        $primaries = $this->getPrimaries();

        $pdata = array();
        foreach ($primaries as $primary) {
            if (!isset($data[$primary])) {
                throw new Keires_DB_Exception(
                    'priamry key is required',
                    Keires_DB_Exception::INVALID_PARAM);
            }
            $pdata[$primary] = $data[$primary];
        }
        return $pdata;
    }

    public function getPrimaryVals($data) {
        return array_values($this->getPrimaryKeyVals($data));
    }

    public function store($data) {
        $primaries = $this->getPrimaries();

        $pdata = $this->getPrimaryVals($data);
        $valid_keys = Keires_DB::getValidKeys($this->tupple_info);

        if (empty($pdata)) {
            $exists = false;
        } else {
            $exists = call_user_func_array(array($this, 'exists'), $pdata);
        }
        if ($exists) {
            // remove primaries from keys
            $valid_keys = array_diff($valid_keys, $primaries);
            $method = 'update';
        } else {
            $method = 'insert';
        }

        $autovals = $this->autoValues($method);
        if (!empty($autovals)) {
            $data = array_merge($data, $autovals);
            $valid_keys = array_merge($valid_keys, array_keys($autovals));
        }

        $typeinfo = Keires_DB::getTypeInfo($this->tupple_info);
        Keires_DB::convTypeAll($data, $typeinfo);

        list($keys, $vals) = Keires_DB::buildKeyVal($data, $valid_keys);
        if ($exists) {
            if (empty($keys)) {
                // table has only columns that are primary keys
                return true;
            }
            $sql = Keires_DB::genUpdateSQL($this->table_name, $keys) .
                ' WHERE ' . $this->makePrimaryCondition();
            foreach ($pdata as $p) {
                $vals[] = $p;
            }
        }
        else {
            $sql = Keires_DB::genInsertSQL($this->table_name, $keys);
        }
        $db = $this->getDB();
        $db->execute($sql, $vals);

        return true;
    }

    public function delete() {
        $args = func_get_args();
        $this->checkPrimaryArgs($args);

        if (count($args) == 0) {
            return array();
        }

        $sql = 'DELETE FROM ' . $this->table_name .
            ' WHERE ' . $this->makePrimaryCondition();
        $db = $this->getDB();
        return $db->getRow($sql, $args);
    }
}

/*
 * Keires_DB_Abstract
 */

class Keires_DB_Abstract {
    static protected $_singletons = array();

    static protected $db_type = Keires_DB::DEFAULT_DB_TYPE;
    protected $_impl = null;

    final private function __construct() {
        if (isset(self::$_singletons[get_called_class()])) {
            throw new Exception('Invalid instantinate Singleton');
        }
        static::initialize();
    }

    protected function initialize() {
        $options = array(
            'db_type' => static::$db_type,
            'table_name' => static::$table_name,
            'tupple_info' => static::$tupple_info,
            );
        $this->_impl = new Keires_DB_Impl($options);
    }

    static final public function getInstance() {
        $class = get_called_class();
        if (!isset(self::$_singletons[$class])) {
            self::$_singletons[$class] = new static();
        }
        return self::$_singletons[$class];
    }

    static protected function getImpl() {
        return self::getInstance()->_impl;
    }

    static public function __callStatic($name, $args) {
        $impl = self::getInstance()->_impl;
        if (!method_exists($impl, $name)) {
            throw new Keires_DB_Exception(
                'method ' . $name . ' is not exists',
                Keires_DB_Exception::UNKNOWN_METHOD);
        }
        return call_user_func_array(array($impl, $name), $args);
    }
}

?>
