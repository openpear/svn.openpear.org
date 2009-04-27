<?php

class Text_VariationProducer implements Iterator {
  private $patterns = null;
  private $remains_producer = null;
  private $current_producer = null;

  private $needs_multiple_producer = null;
  private $current_index = 0;
  private $position = 0;

  public function __construct($string_patterns = null) {
    $this->setPatterns($string_patterns);
  }

  public function setPatterns($string_patterns)
  {
    // 配列だったら、順番に子供producerを作る。
    // {abc[a-z],def[c-z]}
    // 文字列だったら、自分は先頭部分の処理をして、残り部分のproducerを作る。
    // abc
    // abc[a-z]
    // [a-z][cd]
    if (is_array($string_patterns)) {
      $this->needs_multiple_producer = true;
      $this->patterns = $string_patterns;
    } elseif (is_string($string_patterns)) {
      if (preg_match('/^\{('.
                     '(?:[^\}\\\\]|\\\\.)+(?:,(?:(?:[^\}\\\\]|\\\\.)+))*'.
                     ')\}(.*)$/s',
                     $string_patterns, $matches)) {
        $this->needs_multiple_producer = true;
        $this->patterns = self::BraceToArray($matches[1]);
      } elseif (preg_match('/^\[('.
                           '(?:[^\]\\\\]|\\\\.)+'.
                           ')\](.*)$/s',
                           $string_patterns, $matches)) {
        $this->needs_multiple_producer = false;
        $this->patterns = self::characterClassToArray($matches[1]);
      } elseif (preg_match('/^('.
                           '(?:[^\[\{\\\\]|\\\\.)+'.
                           ')(.*)$/s',
                           $string_patterns, $matches) ||
                preg_match('/^(.)(.*)$/s', $string_patterns, $matches)) {
        $this->needs_multiple_producer = false;
        $this->patterns = array(self::ParseString($matches[1]));
      } else {
        throw new Exception('invalid pattern is specified: '. $string_patterns);
      }
      if (isset($matches[2]) && $matches[2] !== "") {
        $this->remains_producer = new Text_VariationProducer($matches[2]);
      }
    }
    $this->rewind();
  }

  public function current()
  {
    $child = "";
    if ($this->needs_multiple_producer) {
      $current = $this->current_producer->current();
    } else {
      $current = $this->patterns[$this->current_index];
    }

    if (is_object($this->remains_producer)) {
      $current .= $this->remains_producer->current();
    }
    return $current;
  }

  public function key()
  {
    return $this->position;
  }

  public function next()
  {
    $this->position++;

    if ($this->remains_producer instanceof StringVariationProducer) {
      $this->remains_producer->next();
      if ($this->remains_producer->valid()) {
        return;
      } else {
        // if invalid, try current->next() or next pattern
        $this->remains_producer->rewind();
      }
    }
    if ($this->current_producer instanceof StringVariationProducer) {
      $current = $this->current_producer->next();
      if ($this->current_producer->valid()) {
        return;
      }
      // if invalid, try next pattern
    }
    $this->current_index++;
    if ($this->needs_multiple_producer &&
        isset($this->patterns[$this->current_index])) {

      $pattern = $this->patterns[$this->current_index];
      $this->current_producer = new Text_VariationProducer($pattern);
    }
  }

  public function rewind()
  {
    $this->current_index = 0;
    $this->position = 0;
    if ($this->needs_multiple_producer) {
      $pattern = $this->patterns[0];
      $this->current_producer = new Text_VariationProducer($pattern);
    }
  }

  public function valid()
  {
    if (isset($this->patterns[$this->current_index])) {
      return true;
    }
    return false;
  }

  private function characterClassToArray($charclass_string)
  {
    $characters = array();
    while ($charclass_string !== "") {
      if (preg_match('/^'.
                     '([^\]\\\\]|\\\\[nrtvf]|\\\\[0-9]{1,3}|\\\\x[0-9A-Fa-f]{1,2})'.
                     '(?:-([^\]\\\\]|\\\\[nrtvf]|\\\\[0-9]{1,3}|\\\\x[0-9A-Fa-f]{1,2}))?'.
                     '(.*)$/s', $charclass_string, $matches)) {
        $start = self::ParseString($matches[1]);
        if ($matches[2] !== "") {
          $end = self::ParseString($matches[2]);
          $start_ord = ord($start);
          $end_ord = ord($end);
          if ($start_ord <= $end_ord) {
            for ($i = $start_ord; $i <= $end_ord; $i++) {
              $characters[] = chr($i);
            }
          }
        } else {
          $characters[] = $start;
        }
        $charclass_string = $matches[3];
      } else {
        // unknown character class string: skip 1st character.
        $charclass_string = substr($charclass_string, 1);
      }
    }
    return $characters;
  }
  private function BraceToArray($inner_brace)
  {
    $ret = split(",", $inner_brace);
    return $ret;
  }
  private function ParseString($str)
  {
    while (preg_match('/^((?:[^\"\$\\\\]|\\\\.)*)([\"\$])/', $str)) {
      $str = preg_replace('/^((?:[^\"\$\\\\]|\\\\.)*)([\"\$])/', '\\1\\\\\\2', $str);
    }
    if ($str === '\\') {
      $str = '\\\\';
    }
    return eval('return "'.$str.'";');
  }
}
