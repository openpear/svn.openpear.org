<?php

require_once 'Keires/DB.php';

/*
  schema:
  CREATE TABLE hogetbl (
      col1    INTEGER PRIMARY KEY,
      col2    TEXT,
      col3    TIMESTAMP,
      col4    BOOLEAN
  );
*/

class DB_hoge extends Keires_DB_Abstract {
    static public $table_name = 'hogetbl';
    static public $tupple_info = array(
        'col1' => array(
            'type' => 'integer',
            'primary' => true,
            ),
        'col2' => array(
            'type' => 'text',
            ),
        'col3' => array(
            'type' => 'timestamp',
            'auto_value' => array(
                'insert' => true,
                'update' => true,
                'value' => "date('c')",
                ),
            ),
        'col4' => array(
            'type' => 'boolean',
            ),
        );

    // custome methods

    static public function findByCol2($col2) {
        $sql =
            'SELECT * FROM ' . self::$table_name .
            ' WHERE col2=?';
        $db = self::getDB();
        return $db->getRow($sql, array($col2));
    }
}

// initialize library
$options = array(
    'types' => array(
        'default' => array(
            'dsn' => 'pgsql:host=localhost;user=koyama;dbname=koyama',
            ),
        ),
    );
Keires_DB::init($options);

// store data

$data = array(
    'col1' => 1,
    'col2' => 'fugahoge',
    // col3 is auto generating
    'col4' => false,
    );
DB_hoge::store($data);

// get by primary key

$col1 = 1;
$result = DB_hoge::getInfo($col1);

// call custom method

$col2 = 'fugahoge';
$result = DB_hoge::findByCol2($col2);
var_dump($result);

// delete by primary key

$col1 = 1;
DB_hoge::delete($col1);