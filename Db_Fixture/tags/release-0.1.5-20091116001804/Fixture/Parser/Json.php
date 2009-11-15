<?php
/**
 * Json
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
require_once dirname(dirname(__FILE__)) . '/Exception.php';

/**
 * Db_Fixture_Parser_Interface
 */
require_once 'Interface.php';

/**
 * Db_Fixture_Parser_Json
 *
 * @category  Db
 * @package   Db_Fixture
 * @version   $id$
 * @copyright 2009 Heavens Hell
 * @author    Heavens hell <heavenshell.jp@gmail.com>
 * @license   New BSD License
 */
class Db_Fixture_Parser_Json implements Db_Fixture_Parser_Interface
{
    /**
     * Pdo
     *
     * @var    mixed
     * @access private
     */
    private $_pdo = null;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Create pdo object
     *
     * @param  mixed $config Path to config file
     * @access public
     * @return Pdo Pdo object
     */
    public function createPdo($config)
    {
        if (!file_exists($config)) {
            throw new Db_Fixture_Exception('Config file not found');
        }
        $file = file_get_contents($config);
        $json = json_decode($file, true);
        if (!is_array($json)) {
            throw new Db_Fixture_Exception('Could not decode json.');
        }

        if (!isset($json['dsn']) || is_null($json['dsn'])) {
            throw new Db_Fixture_Exception('dsn not found.');
        }
        $dsn = $json['dsn'];

        if (!isset($json['username']) || is_null($json['username'])) {
            throw new Db_Fixture_Exception('username not found.');
        }
        $user = $json['username'];

        if (!isset($json['password']) || is_null($json['password'])) {
            throw new Db_Fixture_Exception('password not found.');
        }
        $password = $json['password'];

        // Create Pdo object
        $pdo = new Pdo($dsn, $user, $password);

        return $pdo;
    }

    /**
     * Parse
     *
     * @param  mixed $fixturePath Path to fixture file
     * @access public
     * @return array Fixture
     */
    public function parse($fixturePath)
    {
        $file = file_get_contents($fixturePath);
        return json_decode($file, true);
    }
}
