<?php
/**
 *
 *
 */

/**
 * Wozozo_CaesarCrypt
 *
 */
class Wozozo_CaesarCrypt
{
    protected $number = 0;

    /**
     * __construct
     *
     */
    public function __construct($key = 0) {
    }

    /**
     * setKeyNumber
     *
     */
    public function setKeyNumber($number = 0)
    {
        $number = (int)$number;

        if ($number > 25) {
            throw new Exception('key between 0 and 25');
        }
        $this->number = (int)$number;

    }

    /**
     * encrypt
     *
     */
    public function encrypt($word)
    {
        $word = (string)$word;
        $str_count = strlen($word);

        for ($i = 0; $i <= $str_count; $i++) {
            if (preg_match('_[a-zA-Z]_', $word{$i})) {
                for ($j = 0; $j < $this->number; $j++) {
                    $current_char = $word{$i};
                    $current_char++;
                    $word{$i} = $current_char;
                }
            }
        }

        return $word;
    }

    /**
     * decrypt
     *
     */
    public function decrypt($word)
    {
        $word = (string)$word;
        $str_count = strlen($word);

        for ($i = 0; $i <= $str_count; $i++) {
            if (preg_match('_[a-zA-Z]_', $word{$i})) {
                for ($j = 0; $j < (26 - $this->number); $j++) {
                    $current_char = $word{$i};
                    $current_char++;
                    $word{$i} = $current_char;
                }
            }
        }

        return $word;
    }
}

/*
$word = 'hogeほげhugaふがzZ';

$wozozo = new Wozozo_CaesarCrypt();
$wozozo->setKeyNumber(22);
$en_result = $wozozo->encrypt($word);
$de_result = $wozozo->decrypt($en_result);

var_dump($word, $en_result, $de_result);
 */
