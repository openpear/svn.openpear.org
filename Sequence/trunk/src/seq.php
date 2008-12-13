<?php 
/**
 * 汎用的なリストライブラリ。
 * このライブラリはシンプルであること、オブジェクト指向であること、パワフルであることを目指す。
 * 
 * @package Sequence
 * @author anatoo <study.anatoo@gmail.com>
 * @version 0.0.1
 */

/**
 * Sequenceインスタンスを生成する
 * @param mixed ... リストの要素
 * @return Sequence
 */
function seq()
{
    return new Sequence(func_get_args());
}

/**
 * 配列やイテレータからSequenceインスタンスを生成する
 * @return Sequence
 */
function toseq()
{
    $arr = array();
    foreach (func_get_args() as $arg) foreach ($arg as $elt) $arr[] = $elt;
    return new Sequence($arr);
}


/**
 * 汎用リストクラス
 * 
 */
class Sequence implements ArrayAccess, Countable, IteratorAggregate 
{
    /**
     * リストの要素を持つ配列
     * @var array
     */
    protected $arr;

    /**
     * リストの大きさを持つ
     * @var int
     */
    protected $size;
    
    function __construct(Array $arr)
    {
        $this->arr = $arr;
        $this->size = count($arr);
    }

    /**
     * 要素の数を返す
     *
     * @return int
     */
    function count()
    {
        return $this->size;
    }
    
    /**
     * 添え字を正規化する。
     * 具体的には、添え字がマイナスだった場合、プラスの添え字に変換する
     *
     * @param int $i
     */
    protected function normalizeOffset($i)
    {
        if (!is_int($i)) throw new InvalidArgumentException();
        $len = $this->getLength();
        if ($i < 0) {
            if (!$len) throw new DomainException();
            return $i + $len; 
        }
        return $i;
    }
    
    /**
     * 正規化した添え字が配列の範囲内を指すか検査する
     *
     * @param int $i
     */
    protected function assertOffset($i)
    {
        if ($i >= $this->getLength()) throw new DomainException();
    }
    
    /**
     * 指定した要素を返す
     *
     * @param int $i
     * @return unknown_type
     */
    function offsetGet($i)
    {
        return $this->nth($i);
    }
    
    /**
     * 添え字で指定した要素に代入する
     *
     * @param int $i
     * @param unknown_type $val
     */
    function offsetSet($i, $val)
    {
        if (is_null($i)) {
            return $this->push($val);
        }
        
        $i = $this->normalizeOffset($i);
        $this->assertOffset($i);
        
        $arr[$i] = $val;
    }
    
    /**
     * 添え字が指定する要素が存在するかを確かめる
     *
     * @param int $i
     * @return bool
     */
    function offsetExists($i)
    {   
        $i = $this->normalizeOffset($i);
        
        return isset($this->arr[$i]);
    }
    
    /**
     * リストの要素を削除する
     *
     * @param int $i
     */
    function offsetUnset($i)
    {
        $i = $this->normalizeOffset($i);
        $this->assertOffset($i);
        
        unset($this->arr[$i]);
        $this->arr = array_values($this->arr);
        $this->size--;
    }
    
    function getIterator()
    {
        return new ArrayIterator($this->arr);
    }
    
    /**
     * 末尾に要素を挿入する
     *
     * @param unknown_type $v
     * @return Sequence
     */
    function push($v)
    {
        $this->arr[] = $v;
        $this->size++;
        return $this;
    }

    
   /**
    * 末尾の要素を取り出す
    *
    * @return unknown_type
    */
    function pop()
    {
        if (!$this->count()) throw new RuntimeException();
        $result = array_pop($this->arr);
        $this->size--;
        return $result;
    }

    /**
     * 並びを逆にしたSequenceを生成する
     *
     * @return Sequence
     */
    function reverse()
    {
        return new self(array_reverse($this->arr));
    }

    /**
     * ゲッターメソッドの結果を返す
     *
     * @param string $name
     * @return unknown_type
     */
    function __get($name)
    {
        if (!method_exists($this, 'get' . $name)) throw new BadMethodCallException;
        return $this->{'get' . $name}();
    }

    /**
     * セッターメソッドの結果を返す
     *
     * @param string $name
     * @param unknown_type $val
     * @return unknown_type
     */
    function __set($name, $val)
    {
        if (!method_exists($this, 'set' . $name)) throw new BadMethodCallException;
        return $this->{'set' . $name}($val);
    }

    /**
     * 要素の数を返す
     *
     * @return int
     */
    function getLength()
    {
        return $this->count();
    }


    /**
     * リストの長さを切り詰める。
     * $iがリストの長さよりも長い場合、nullが挿入される。
     *
     * @param int $i
     * @return Sequence
     */
    function setLength($i)
    {
        if (!is_int($i) || $i < 0) throw new InvalidArgumentException;
        
        $this->arr = array_slice($this->arr, 0, $i);
        for ($i = 0, $m = $i - count($this->arr); $i < $m ; $i++) {
            $this->arr[] = null;
        }
        $this->size = $i;
        return $this;
    }

    /**
     * 要素を得る
     *
     * @param int $i
     * @return unknown_type
     */
    function nth($i)
    {
        $i = $this->normalizeOffset($i);
        $this->assertOffset($i);
        
        return $this->arr[$i];
    }

    /**
     * 最初の要素を得る
     *
     * @return unknown_type
     */
    function getFirst()
    {
        return $this->nth(0);
    }

    /**
     * 二番目の要素を得る
     *
     * @return unknown_type
     */
    function getSecond()
    {
        return $this->nth(1);
    }
    
    /**
     * 三番目の要素を得る
     *
     * @return unknown_type
     */
    function getThird()
    {
        return $this->nth(2);
    }

    /**
     * 末尾の要素を得る
     *
     * @return unknown_type
     */
    function getTail()
    {
        return $this->nth(-1);
    }

    /**
     * 渡された変数にリストの要素を順に代入する
     *
     * @return Sequence
     */
    function tovar(&$_0, &$_1 = null, &$_2 = null, &$_3 = null, &$_4 = null,
                   &$_5 = null, &$_6 = null, &$_7 = null, &$_8 = null, &$_9 = null)
    {
        $args_length = func_num_args();
        foreach (range(0, $args_length - 1) as $i) {
            $iame = '_' . $i;
            $$iame = $this->nth($i);
        }
        return $this;
    }

    /**
     * 添え字として有効であるかを返す。
     * マイナスも有効
     *
     * @param int $i
     * @return bool
     */
    function in($i)
    {
        $i = $this->normalizeOffset($i);
        return $i < $this->getLength();
    }

    /**
     * リストを二つに分割したSequenceを返す
     *
     * @return Sequence
     */
    function cut($i)
    {
        $i = $this->normalizeOffset($i);
        $this->assertOffset($i);
        return seq(toseq(array_slice($this->arr, 0, $i)),
                   toseq(array_slice($this->arr, $i)));
    }
    
    /**
     * リストの最初と残りの要素とに分割したSequenceを返す
     *
     * @return Sequenceを返す
     */
    function unclip()
    {
        return $this->cut(1);
    }

    /**
     * リストの一部の要素を返す
     *
     * @param int $offset
     * @paramm int $limit
     * @return Sequence
     */
    function slice($offset, $limit = null)
    {
        $offset = $this->normalizeOffset($offset);
        $this->assertOffset($offset);
        
        if (is_null($limit)) {
            return toseq(array_slice($this->arr, $offset));
        }

        if (!is_int($limit)) throw new InvalidArgumentException;

        return toseq(array_slice($this->arr, $offset, $limit));
    }

    /**
     * リストの最初の要素以外を返す
     *
     * @return Sequence
     */
    function rest()
    {
        return $this->slice(1);
    }

    /**
     * リストを半分に分割する。
     * リストの長さが奇数だった場合、最初の半分のリストの数のほうが小さくなる。
     *
     * @return Sequence
     */
    function halves()
    {
        if ($this->getLength() === 0) throw new OutOfBoundsException;
        $len = $this->getLength();
        if ($len % 2) $len -= 1;

        return $this->cut($len / 2);
    }

    /**
     * 添え字で指定した要素で構成されるSequenceを生成する
     *
     * @param int ...
     * @return Sequence
     */
    function pick()
    {
        $seq = seq();
        foreach (func_get_args() as $i) {
            $seq->push($this->nth($i));
        }
        return $seq;
    }

    /**
     * リストの要素をすべてvar_dumpする
     *
     * @return Sequence
     */
    function dump()
    {
        echo 'seq(' . PHP_EOL;
        foreach ($this->arr as $i => $elt) {
            $elt instanceof self ? $elt->dump() : var_dump($elt);
            if ($i !== $this->count() - 1) echo ',' . PHP_EOL;
        }
        echo ')' . PHP_EOL;
        return $this;
    }

    /**
     * リストの要素をひとつずつ与えられた関数に適用し、
     * その結果を新たなSequenceに格納して返す
     *
     * @param callback $func
     * @param unknown_type ... 
     * @return Sequence
     */
    function map($func)
    {
        $this->assertCallable($func);
        $args_proto = $this->buildArgsProto(func_get_args());
                
        $ret = seq();
        foreach ($this as $elt) {
            $args = array_merge($args_proto, array($elt));
            $ret->push(call_user_func_array($func, $args));
        }
        return $ret;
    }
    
    /**
     * リストの要素をひとつずつ与えられた関数に適用し、
     * その結果がtrueである要素を格納した新たなSequenceを返す
     *
     * @param callback $func
     * @param unknown_type ... 
     * @return Sequence
     */
    function filter($func)
    {
        $this->assertCallable($func);
        $args_proto = $this->buildArgsProto(func_get_args());

        $ret = seq();
        foreach ($this as $elt) {
            $args = array_merge($args_proto, array($elt));
            if (call_user_func_array($func, $args)) $ret->push($elt); 
        }
        return $ret;
    }

    /**
     * 
     *
     * @param callback $func
     * @return unknown
     */
    function reduce($func)
    {
        $this->assertCallable($func);
        if (!$this->getLength()) throw new RuntimeException;
        $arr = array_reverse($this->toArray());
        while (count($arr) > 1) {
            array_push($arr, call_user_func($func, array_pop($arr), array_pop($arr)));
        }
        return $arr[0];
    }

    /**
     * 
     *
     * @param callback $func
     * @return bool
     */
    function all($func)
    {
        $this->assertCallable($func);
        $args_proto = $this->buildArgsProto(func_get_args());

        foreach ($this as $elt) {
            $args = array_merge($args_proto, array($elt));
            if (!call_user_func_array($func, $args)) return false;
        }
        return true;
    }

    /**
     *
     * @param callback $func
     * @return bool
     */
    function any($func)
    {
        $this->assertCallable($func);
        $args_proto = $this->buildArgsProto(func_get_args());

        foreach ($this as $elt) {
            $args = array_merge($args_proto, array($elt));
            if (call_user_func_array($func, $args)) return true;
        }
        return false;
    }

    /**
     * リストの要素を与えられたコールバックに一つ一つ渡していく。
     *
     * @param callback $func
     * @return Sequence
     */
    function each($func)
    {
        $this->assertCallable($func);
        $args_proto = $this->buildArgsProto(func_get_args());

        foreach ($this as $elt) {
            $args = array_merge($args_proto, array($elt));
            call_user_func_array($func, $args);
        }
        return $this;
    }

    protected function assertCallable($func)
    {
        if (!is_callable($func)) throw new InvalidArgumentException;
    }

    protected function buildArgsProto(Array $arr)
    {
        if (count($arr) > 1) {
            array_shift($arr);
            return $arr;
        }
        return array();
    }

    /**
     * リストをarrayにして返す
     *
     * @return array
     */
    function toArray()
    {
        return $this->arr;
    }

    /**
     * リストの最初の要素を抜き出す
     *
     * @return unknown_type
     */
    function shift()
    {
        if (!$this->count()) throw new RuntimeException;
        $result = array_shift($this->arr);
        $this->size--;
        return $result;
    }

    /**
     * リストの最初に要素を挿入する
     *
     * @param unknown_type $v
     * @return Sequence
     */
    function unshift($v)
    {
        array_unshift($this->arr, $v);
        $this->size++;
        return $this;
    }

    /**
     * リストのインデックスのリストを返す
     *
     * @return Sequence
     */
    function indexes()
    {
        return toseq(range(0, $this->count() ? $this->count() - 1 : 0));
    }
    
    /**
     * リストの要素を繰り返したSequenceを生成し返す
     * 
     * @param int $i 
     * @return Sequence
     */
    function repeat($i)
    {
        if (!is_int($i) || $i < 1) throw new InvalidArgumentException(); 
        
        $arr = array();
        for (; $i > 0; $i--) $arr[] = $this->arr;
        return call_user_func_array('toseq', $arr);
    }
    
    /**
     * リストがある要素を持っているかどうかを返す
     *
     * @param unknown_type $elt
     * @return bool
     */
    function has($elt)
    {
        foreach ($this->arr as $_elt) if ($elt === $_elt) {
            return true;
        }
        return false;
    }
    
    /**
     * false以外の要素を持つSequenceを生成して返す
     *
     * @return Sequence
     */
    function harvest()
    {
        $arr = array();
        foreach ($this->arr as $elt) if ($elt !== false) {
            $arr[] = $elt;
        }
        return toseq($arr);
    }
    
}
