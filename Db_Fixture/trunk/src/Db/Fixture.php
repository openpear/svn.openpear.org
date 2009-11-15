<?php
/**
 * Fixture
 *
 * PHP version 5.2
 *
 * Copyright (c) 2009 Heavens hell, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Heavens hell nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Db
 * @package   Db_Fixture
 * @version   $id$
 * @copyright 2009 Heavens hell
 * @author    Heavens hell <heavenshell.jp@gmail.com>
 * @license   New BSD License
 */

/**
 * @see Db_Fixture_Exception
 */
require_once dirname(__FILE__) . '/Fixture/Exception.php';

/**
 * Db_Fixture
 *
 * @category  Db
 * @package   Db_Fixture
 * @version   $id$
 * @copyright 2009 Heavens Hell
 * @author    Heavens hell <heavenshell.jp@gmail.com>
 * @license   New BSD License
 */
class Db_Fixture
{
    /**
     * Db_Fixture version
     */
    const VERSION = '0.1.4';

    /**
     * Fixture
     *
     * @var    mixed
     * @access private
     */
    private static $_fixtures = null;

    /**
     * Pdo
     *
     * @var    mixed
     * @access private
     */
    private static $_pdo = null;

    /**
     * Last insert id
     *
     * @var    mixed
     * @access private
     */
    private static $_lastInsertedId = null;

    /**
     * Inserted data
     *
     * @var    mixed
     * @access private
     */
    private static $_insertedData = null;

    /**
     * Set pdo
     *
     * @param  Pdo $pdo Pdo object
     * @access public
     * @return Db_Fixture Fluent interface
     */
    public static function setPdo(Pdo $pdo)
    {
        self::$_pdo = $pdo;
        return new self();
    }

    /**
     * Load parser class
     *
     * @param  mixed $fixture Fixture info
     * @param  mixed $config Path to database config file
     * @access public
     * @return Db_Fixture Fluent interface
     */
    public static function load($fixtures, $config = null)
    {
        if (is_string($fixtures)) {
            $fixtures = array(
                pathinfo($fixtures, PATHINFO_FILENAME) => $fixtures
            );
        }

        foreach ($fixtures as $fixtureName => $fixturePath) {
            if (!file_exists($fixturePath)) {
                throw new Db_Fixture_Exception($fixturePath . ' not found.');
            }

            $extension = pathinfo($fixturePath, PATHINFO_EXTENSION);
            if (is_null($config)) {
                // Config path is same directory as testdata.
                // config/database.{json,ini,xml,yml,csv}
                $config = dirname(dirname($fixturePath)) . DIRECTORY_SEPARATOR
                        . 'config' . DIRECTORY_SEPARATOR . 'database.'
                        . strtolower($extension);
            }
            if (!file_exists($config)) {
                throw new Db_Fixture_Exception($config . ' not found.');
            }

            $parser = self::_getParser($extension);

            if (is_null(self::$_pdo)) {
                // Get pdo object
                self::$_pdo = $parser->createPdo($config);
            }

            // Parse fixture file
            self::$_fixtures = $parser->parse($fixturePath);

            return new self();
        }
    }

    /**
     * Insert
     *
     * @access public
     * @return Db_Fixture Fluent interface
     */
    public static function insert()
    {
        $fixtures = self::$_fixtures;
        if (is_null($fixtures)) {
            throw new Db_Fixture_Exception('Fixture does not set.');
        }

        foreach ($fixtures as $tablename => $rows) {
            $autoIncrement = null;
            $primarykey    = null;
            $insertedData  = null;
            foreach ($rows as $row => $data) {
                // Autoincrement
                if ($row === 'auto_increment') {
                    $autoIncrement = $data;
                    continue;
                }
                if ($row === 'primary_key') {
                    $primarykey = $data;
                    continue;
                }
                $sql         = 'INSERT INTO ' . $tablename;
                $columns     = null;
                $placeholder = null;
                $bindValues  = null;
                foreach ($data as $key => $value)
                {
                    $columns[]     = $key;
                    $placeholder[] = ':' . $key;
                    $bindValues[':' . $key] = $value;
                }
                $sql .= '(' . implode(',', $columns) . ') VALUES (';
                $sql .= implode(',', $placeholder) . ')';

                $type = null;
                $stmt = self::$_pdo->prepare($sql);
                foreach ($bindValues as $param => $val) {
                    if (is_numeric($val)) {
                        $type = PDO::PARAM_INT;
                    } else if (is_string($val)) {
                        $type = PDO::PARAM_STR;
                    } else if (is_bool($val)) {
                        $type = PDO::PARAM_BOOL;
                    } else if (is_null($val) || $val === '') {
                        $type = PDO::PARAM_NULL;
                    }

                    // Insert datetime when fixture is System.Data:Format.
                    // strpos() returns false when needle not found.
                    if (strpos($val,'System.Date') !== false) {
                        $pos    = strpos($val, ':');
                        $format = 'Y-m-d H:i:s';
                        if ($pos !== false) {
                            $format = substr($val, $pos + 1, strlen($val));
                        }
                        $val = date($format, time());
                    }

                    // If table does not use auto increment,
                    // set primary key column data.
                    if (!is_null($primarykey)) {
                        $columnName = ltrim($param, ':');
                        foreach ($primarykey as $value) {
                            if ($columnName === $value) {
                                $insertedData[$tablename][$row][$value] = $val;
                            }
                        }
                    }

                    $stmt->bindValue($param, $val, $type);

                }
                $ret = $stmt->execute();
                $id  = self::$_pdo->lastInsertId($autoIncrement);
                if (!is_null($autoIncrement)) {
                    // Add id for using in delete()
                    self::$_lastInsertedId[$tablename][$autoIncrement][] = $id;
                }

                $stmt = null;
            }

        }
        self::$_insertedData = $insertedData;
        return new self();
    }

    /**
     * after
     *
     * @access public
     * @return Db_Fixture Fluent interface
     */
    public static function after()
    {
        self::$_lastInsertedId = null;
        self::$_pdo      = null;
        self::$_fixtures = null;

        return new self();
    }

    /**
     * Delete all inserted data
     *
     * @access public
     * @return Db_Fixture Fluent interface
     */
    public static function delete()
    {
        if (is_null(self::$_lastInsertedId)) {
            return new self();
        }
        foreach (self::$_lastInsertedId as $tablename => $columns) {
            $sql = 'DELETE FROM ' . $tablename . ' WHERE ';
            foreach ($columns as $column => $datas) {
                $where = '';
                $i     = 1;
                $bindValues = null;
                foreach ($datas as $value) {
                    $where .= '?,';
                    $bindValues[$i] = $value;
                    $i ++;
                }
                $where = trim(rtrim(rtrim($where), ','));
                $sql .= $column . ' IN (' .  $where . ')';
                $stmt = self::$_pdo->prepare($sql);
                foreach ($bindValues as $key => $val) {
                    $stmt->bindValue($key, $val);
                }
                $stmt->execute();
                $stmt = null;
            }
        }

        if (is_null(self::$_insertedData)) {
            return new self();
        }

        $insertedData = self::$_insertedData;
        foreach ($insertedData as $tablename => $rows) {
            foreach ($rows as $columns => $data) {
                $sql         = 'DELETE FROM ' . $tablename . ' WHERE ';
                $columns     = null;
                $placeholder = null;
                $bindValues  = null;
                $conditions  = '';
                foreach ($data as $key => $value)
                {
                    $conditions .= $key . ' = :' . $key . ' AND ';
                    $columns[]     = $key;
                    $placeholder[] = ':' . $key;
                    $bindValues[':' . $key] = $value;
                }
                $sql .= rtrim(trim($conditions), ' AND');
                $stmt = self::$_pdo->prepare($sql);
                foreach ($bindValues as $param => $val) {
                    $stmt->bindValue($param, $val);

                }
                $ret  = $stmt->execute();
                $stmt = null;
            }
        }

        return new self();
    }

    /**
     * Get fixture
     *
     * @access public
     * @return mixed Fixture
     */
    public static function fixtures()
    {
        return self::$_fixtures;
    }

    /**
     * Get connection
     *
     * @access public
     * @return Pdo Pdo object
     */
    public static function getConnection()
    {
        return self::$_pdo;
    }

    /**
     * Get last insert id
     *
     * @access public
     * @return mixed Last inserted id
     */
    public static function getLastInsertedId()
    {
        return self::$_lastInsertedId;
    }

    /**
     * Execute sql file
     *
     * @param  mixed $file Path to sql file
     * @access public
     * @return mixed Db_Fixture Fluent interface
     */
    public static function execute($file, $config = null)
    {
        if (!file_exists($file)) {
            return new self();
        }

        if (!is_null($config)) {
            $extension = pathinfo($config, PATHINFO_EXTENSION);

            self::$_pdo = self::_getParser($extension)->createPdo($config);
        }
        $pdo = self::$_pdo;

        if (is_null($pdo)) {
            return new self();
        }

        $sql  = file_get_contents($file);
        $stmt = $pdo->prepare($sql);
        $ret  = $stmt->execute();
        $stmt = null;

        return new self();
    }

    /**
     * Get parser object
     *
     * @param  mixed $name Parser name
     * @access private
     * @return Db_Fixture_Parser Parser object
     */
    private static function _getParser($name)
    {
        $className  = __CLASS__ . '_Parser_' . ucfirst($name);
        $parserPath = str_replace('_', DIRECTORY_SEPARATOR, $className);
        $parserPath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR
                    . $parserPath . '.php';

        if (!class_exists($className, false)) {
            require_once $parserPath;
        }

        // Create instance
        $parser = new $className();

        return $parser;
    }
}
