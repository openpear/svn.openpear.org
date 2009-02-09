<?php

class Validator
{


TEL
	
URL
	
MAIL
	メール
ZIP
	NNN-NNNN
DATE
	YYYY-MM-DD
TIME
	HH:mm:ss
DATETIME
	YYYY-MM-DD HH:mm:ss
DIGIT
	数字
ALPHA
	英字
REQUIRE
	必須
LENGTH
	文字列の最小大長
HIRAKANA
	かな
KATAKANA
	カナ
HANKAKU
	半角
ARROW
	文字列が特定の文字を含んでいなければならない(不正な文字の検出に使用)
DENY
	文字列が特定の文字を含んでいてはいけない(不正な文字の検出に使用)
TRIM
	検証の前に前後の空白を除去するか否か

ICBM

FLOAT

HEX

ARRAY

IP

EMPTY

REGEX

define( "CS_TO_HAN",   1 );
define( "CS_TO_ZEN",   2 );
define( "CS_TO_UPPER", 4 );
define( "CS_TO_LOWER", 8 );

    public function __construct()
    {

    }

    public function __call( $method_name, $arguments )
    {

        if ( preg_match( '/^set/', $method_name ) ) {
            $this->valid( $arguments );
        }
        if ( preg_match( '/^valid/', $method_name ) ) {
            return $this->valid( $arguments );
        }
        if ( preg_match( '/^require/', $method_name ) ) {
            $this->valid( $arguments );
        }
        if ( preg_match( '/^length/', $method_name ) ) {
            $this->valid( $arguments );
        }
        if ( preg_match( '/^kana/', $method_name ) ) {
            $this->valid( $arguments );
        }

        return false;
    }

    public function valid( $name, $flag, $message )
    {
    
    }

}
?>
