<?php

class Text_VariationProducer implements Iterator {
  const UTF16 = '[\x00-\xd7\xe0-\xff][\x00-\xff]';
  const UTF_16 = '[\x00-\xd7\xe0-\xff][\x00-\xff]';
  const UTF8 = '{[\x00-\x7f],[\xc2-\xdf][\x80-\xbf],[\xe0][\xa0-\xbf][\x80-\xbf],[\xe1-\xec][\x80-\xbf][\x80-\xbf],\xed[\x80-\x9f][\x80-\xbf],[\xee\xef][\x80-\xbf][\x80-\xbf]}';
  const UTF_8 = '{[\x00-\x7f],[\xc2-\xdf][\x80-\xbf],[\xe0][\xa0-\xbf][\x80-\xbf],[\xe1-\xec][\x80-\xbf][\x80-\xbf],\xed[\x80-\x9f][\x80-\xbf],[\xee\xef][\x80-\xbf][\x80-\xbf]}';
  const SJIS = '{[\0-\x7e],[\x81-\x9f\xe0-\xef][\x40-\x7e\x80-\xfc],[\xa1-\xdf]}';
  const SHIFT_JIS = '{[\0-\x7e],[\x81-\x9f\xe0-\xef][\x40-\x7e\x80-\xfc],[\xa1-\xdf]}';
  const SJIS_WIN = '{[\0-\x7e],[\x81-\x9f\xe0-\xfc][\x40-\x7e\x80-\xfc],[\xa1-\xdf]}';
  const CP932 = '{[\0-\x7e],[\x81-\x9f\xe0-\xfc][\x40-\x7e\x80-\xfc],[\xa1-\xdf]}';
  const EUCJP = '{[\0-\x7e],[\xa1-\xfe][\xa1-\xfe],\x8e[\xa1-\xfe],\x8f[\xa1-\xfe][\xa1-\xfe]}';
  const EUC_JP = '{[\0-\x7e],[\xa1-\xfe][\xa1-\xfe],\x8e[\xa1-\xfe],\x8f[\xa1-\xfe][\xa1-\xfe]}';
  const EUCJP_WIN = '{[\0-\x7e],[\xa1-\xfe][\xa1-\xfe],\x8e[\xa1-\xfe],\x8f[\xa1-\xfe][\xa1-\xfe]}';
  const CP51932 = '{[\0-\x7e],[\xa1-\xfe][\xa1-\xfe],\x8e[\xa1-\xfe]}';

  private $patterns = null;
  private $rest_producer = null;
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
      if ($string_patterns === "") {
        $this->needs_multiple_producer = false;
        $this->patterns = array("");
      } elseif (preg_match('/^\{('.
                     '(?:[^\}\\\\]|\\\\.)+'.
                     ')\}(.*)$/s',
                     $string_patterns, $matches)) {
        // 中カッコで囲まれた部分
        $this->needs_multiple_producer = true;
        $this->patterns = self::BraceToArray($matches[1]);
      } elseif (preg_match('/^\[('.
                           '(?:[^\]\\\\]|\\\\.)+'.
                           ')\](.*)$/s',
                           $string_patterns, $matches)) {
        // 角カッコで囲まれた部分
        $this->needs_multiple_producer = false;
        $this->patterns = self::characterClassToArray($matches[1]);
      } elseif (preg_match('/^('.
                           '(?:[^\[\{\\\\]|\\\\.)+'.
                           ')(.*)$/s',
                           $string_patterns, $matches) ||
                preg_match('/^(.)(.*)$/s', $string_patterns, $matches)) {
        // 「開き角カッコ」、「開き中カッコ」以外の文字の連続
        // または中途半端な1文字（対応の取れていない開き角カッコなど）
        $this->needs_multiple_producer = false;
        $this->patterns = array(self::ParseString($matches[1]));
      } else {
        throw new Exception('invalid pattern is specified: '. $string_patterns);
      }
      if (isset($matches[2]) && $matches[2] !== "") {
        $this->rest_producer = new Text_VariationProducer($matches[2]);
      }
    }
    $this->rewind();
  }

  public function current()
  {
    $child = "";
    if ($this->current_producer instanceof Text_VariationProducer) {
      $current = $this->current_producer->current();
    } else {
      $current = $this->patterns[$this->current_index];
    }
    if ($this->rest_producer instanceof Text_VariationProducer) {
      $current .= $this->rest_producer->current();
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

    if ($this->rest_producer instanceof Text_VariationProducer) {
      $this->rest_producer->next();
      if ($this->rest_producer->valid()) {
        return;
      } else {
        // if invalid, try current->next() or next pattern
        $this->rest_producer->rewind();
      }
    }
    if ($this->current_producer instanceof Text_VariationProducer) {
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

  private static function characterClassToArray($charclass_string)
  {
    $negate_characters = false;
    $characters = array();
    if (preg_match('/^\^(.*)$/s', $charclass_string, $matches)) {
      $negate_characters = true;
      $charclass_string = $matches[1];
    }
    for ($i = 0; $i <= 0xff; $i++) {
      $characters_occurred[$i] = 0;
    }

    while ($charclass_string !== "") {
      if ($charclass_string === false) {exit;}
      if (preg_match('/^'.
                     '([^\]\\\\]|\\\\[0-9]{1,3}|\\\\x[0-9A-Fa-f]{1,2}|\\\\.)'.
                     '(?:-([^\]\\\\]|\\\\[0-9]{1,3}|\\\\x[0-9A-Fa-f]{1,2}|\\\\.))?'.
                     '(.*)$/s', $charclass_string, $matches)) {
        $start = self::ParseString($matches[1]);
        if ($matches[2] !== "") {
          $end = self::ParseString($matches[2]);
          $start_ord = ord($start);
          $end_ord = ord($end);
          if ($start_ord > $end_ord) {
            $start_ord = $end_ord;
            $end_ord = ord($start);
          }
          for ($i = $start_ord; $i <= $end_ord; $i++) {
            $characters_occurred[$i] = 1;
          }
        } else {
          $characters_occurred[ord($start)] = 1;
        }
        $charclass_string = $matches[3];
      } elseif (preg_match('/^(.)(.*)$/s', $charclass_string, $matches)) {
        // unknown character class string: skip 1st character.
        $characters_occurred[ord($matches[1])] = 1;
        $charclass_string = $matches[2];
      }
    }
    for ($i = 0; $i <= 0xff; $i++) {
      if (($negate_characters && !$characters_occurred[$i]) ||
          (!$negate_characters && $characters_occurred[$i])) {
        $characters[] = chr($i);
      }
    }
    return $characters;
  }
  private static function BraceToArray($inner_brace)
  {
    $ret = array();
    while (1) {
      // 区切り文字の「,」または文字列最後まで読む。「\,」でエスケープできる。
      if (preg_match('/^('.
                     '(?:[^,\\\\]|\\\\.|\\\\)*'.
                     ')(?:,(.*))?$/s', $inner_brace, $matches)) {
        $ret[] = self::ParseString($matches[1]);
        if (!isset($matches[2])) {
          break;
        }
        $inner_brace = $matches[2];
      }
    }
    return $ret;
  }
  private static function ParseString($str)
  {
    $parsed_string = "";
    while ($str !== "") {
      if (preg_match('/^([^\\\\]+)(.*)$/s', $str, $matches)) {
        // \以外の文字連続
        $parsed_string .= $matches[1];
        $str = $matches[2];
      } elseif (preg_match('/^((?:\\\\[nrtvf]|\\\\[0-9]{1,3}|\\\\x[0-9A-Fa-f]{1,2})+)(.*)$/s', $str, $matches)) {
        // \からはじまる、PHPが解釈可能な文字列表現
        $parsed_string .= eval('return "'.$matches[1].'";');
        $str = $matches[2];
      } elseif (preg_match('/^\\\\(.)(.*)$/s', $str, $matches) ||
                preg_match('/^(.)(.*)$/s', $str, $matches)) {
        // 他の何にもマッチしない\であれば、次の文字を残す
        // または、解釈できない文字があれば（単体の\など）そのまま残す
        $parsed_string .= $matches[1];
        $str = $matches[2];
      } else {
        throw new Exception('invalid pattern is specified: '. $str);
      }
    }
    return $parsed_string;
  }
}
