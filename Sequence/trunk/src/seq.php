<?php 
/**
 * 汎用的なリストライブラリ。
 * このライブラリは、オブジェクト指向であること、扱いやすい事、パワフルであることを目指す。
 * 
 * @package Sequence
 * @author anatoo <study.anatoo@gmail.com>
 * @version 0.0.1
 */

/**
 * Sequenceインスタンスを生成する。
 * 引数にはリストの要素を渡す。
 * 
 * @return Sequence
 */
function seq()
{
    return new Sequence(func_get_args());
}

/**
 * 配列やイテレータからリストを生成する。
 * 引数にはリストを生成する元となるものをわたす。
 * この関数は渡された全ての引数を要素として持つリストを生成する。
 * 
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
        // $seq[] = $val;の場合
        if (is_null($i)) {
            return $this->push($val);
        }
        
        $i = $this->normalizeOffset($i);
        $this->assertOffset($i);
        
        $this->arr[$i] = $val;
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
     * 並びを逆にしたリストを生成する
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
     * $iがリストの長さよりも長い場合、nullが挿入される
     *
     * @param int $i
     * @return Sequence
     */
    function setLength($i)
    {
        if (!is_int($i) || $i < 0) throw new InvalidArgumentException;
        
        $this->arr = array_slice($this->arr, 0, $i);
        for ($j = 0, $m = $j - count($this->arr); $j < $m ; $j++) {
            $this->arr[] = null;
        }
        $this->size = $i;
        return $this;
    }
    
    /**
     * リストの長さを切り詰める。
     * $this->setLength()へのエイリアス
     *
     * @param int $i
     * @return Sequence
     */
    function lengthen($i)
    {
        return $this->setLength($i);
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
    function getLast()
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
     * リストを二つに分割したリストを生成する
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
     * リストの最初と残りの要素とに分割したリストを生成する
     *
     * @return Sequence
     */
    function unclip()
    {
        return $this->cut(1);
    }

    /**
     * リストの一部の要素を格納したリストを生成する
     *
     * @param int $offset
     * @param int $limit
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
     * リストの最初の要素以外のリストを生成する
     *
     * @return Sequence
     */
    function rest()
    {
        return $this->slice(1);
    }

    /**
     * リストを半分に分割したリストを生成する。
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
     * 添え字で指定した要素で構成されるリストを生成する
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
     * その結果を格納したリストを生成する
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
     * その結果がtrueである要素を格納した新たなリストを生成する
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
     * リストの要素を与えられたコールバックに一つ一つ渡していく
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
     * 自身のインデックスのリストを生成する
     *
     * @return Sequence
     */
    function indexes()
    {
        return toseq(range(0, $this->count() ? $this->count() - 1 : 0));
    }
    
    /**
     * リストの要素を繰り返したリストを生成する
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
     * false以外の要素を持つリストを生成する
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
    
    /**
     * リストをグループ化したリストを生成する
     *
     * @param int $i
     * @return Sequence
     */
    function group($i)
    {
        if (!is_int($i) || $i < 1) throw new InvalidArgumentException();
        
        $ret = seq();
        foreach (array_chunk($this->arr, $i) as $elt) {
            $ret[] = toseq($elt);
        }
        return $ret;
    }

    /**
     * リストの末尾にリストを追加し、自身を返す
     *
     * @param Sequence $seq
     * @return Sequence
     */
    function append(Sequence $seq)
    {
        foreach ($seq as $elt) $this->push($seq);
        
        return $this;
    }

    /**
     * リストの要素をすべて繋げたリストを生成する。
     * 要素がSequenceではない場合RuntimeExceptionが投げられる。
     *
     */
    function concat()
    {
        $ret = seq();
        foreach ($this as $elt) if ($elt instanceof self) {
            $ret->append($elt);
        }
        else {
            throw new RuntimeException;
        }
        return $ret;
    }
    
    /**
     * リストの最初のいくつかの要素を格納したリストを生成する
     *
     * @param int $i
     * @return Sequence
     */
    function head($i)
    {
        if (!is_int($i) || $i < 0) throw new InvalidArgumentException();
        
        return $this->slice(0, $i);
    }
    
    /**
     * リストの末尾を含む要素を格納したリストを生成する。
     * 
     * 例：
     * <code>
     * seq(1, 2, 3, 4, 5)->tail(2); // => seq(4, 5)
     * </code>
     * 
     * @param int $i
     * @return Sequence
     */
    function tail($i)
    {
        if (!is_int($i) || $i < 0)throw new InvalidArgumentException();
        
        return $this->slice(-$i, $i);
    }

    /**
     * 自身のリストと引数で与えられたSequenceの要素を持つリストを要素として持つリストを生成する。
     * 
     * 例:
     * <code>
     * seq(1, 2, 3)->zip(seq("a", "b")); // => seq(seq(1, "a"), seq(2, "b"));
     * </code>
     *
     * @param Sequence $seq
     * @return Sequence
     */
    function zip(Sequence $seq)
    {
        $ret = seq();
        $len = min(array(count($this), count($seq)));
        for ($i = 0; $i < $len; $i++) {
            $ret->push(seq($this->nth($i), $seq->nth($i)));
        }
        return $ret;
    }
    
    /**
     * リストの中から最も大きな数を返す
     *
     * @return int|float
     */
    function max()
    {
        return max($this->toArray());
    }
    
    /**
     * リストの中から最も小さな数を返す
     *
     * @return int|float
     */
    function min()
    {
        return min($this->toArray());
    }
    
    /**
     * リストの中から最も大きな数と小さな数を要素として持つリストを生成する
     *
     * @return Sequence
     */
    function maxmin()
    {
        return seq($this->max(), $this->min());
    }
    
    /**
     * リストの要素の積を返す
     *
     * @return int|float
     */
    function product()
    {
        return array_product($this->toArray());
    }
    
    /**
     * リストの要素の合計値を返す
     * 
     * @return int|float
     */
    function sum()
    {
        return array_sum($this->toArray());
    }
    
    /**
     * リストの末尾に与えられた要素を持つリストを生成する。
     * 
     * 例:
     * <code>
     * seq(1, 2, 3)->suffix(4); // => seq(1, 2, 3, 4);
     * </code>
     *
     * @param unknown_type $elt
     * @return Sequence
     */
    function suffix($elt)
    {
        $arr = $this->toArray();
        array_push($arr, $elt);
        return toseq($arr);
    }
    
    /**
     * リストの最初に与えられた要素を持つリストを生成する。
     * 
     * 例:
     * <code>
     * seq(1, 2, 3)->prefix(0); // => seq(0, 1, 2, 3);
     * </code>
     *
     * @param unknown_type $elt
     * @return Sequence
     */
    function prefix($elt)
    {
        $arr = $this->toArray();
        array_unshift($arr, $elt);
        return toseq($arr);
    }
    
    /**
     * 与えられた要素をリストの最初や末尾から取り除いたリストを生成する。
     *
     * @return Sequence
     */
    function trim()
    {
        $args = func_get_args();
        $ret = clone $this;
        while($ret->count() > 0) if (in_array($ret->first, $args, true)) {
            $ret->shift();
        }
        else {
            break;
        }
    
        while($ret->count() > 0) if (in_array($ret->last, $args, true)) {
            $ret->pop();
        }
        else {
            break;
        }
        
        return $ret;
    }
    
    /**
     * リストの全てを与えられた要素で埋めて自身を返す
     *
     * @param unknown_type $elt
     * @return Sequence
     */
    function fill($elt)
    {
        foreach ($this as $i => $v) {
            $this[$i] = $elt;
        }
        return $this;
    }
    
    /**
     * リストから与えられた要素を取り除いたリストを生成する
     *
     * @return Sequence
     */
    function remove()
    {
        $args = func_get_args();
        $ret = seq();
        foreach ($this as $elt) if (array_in($elt, $args, true)) {
            $ret->push($elt);
        }
        return $ret;
    }
    
    /**
     * リストのある要素を他の部分にコピーして、自身を返す。
     * 
     * 例:
     * <code>
     * seq(1, 2, 3)->move(0, 2); // => seq(1, 2, 1);
     * </code>
     *
     * @param int $i
     * @param int $j
     * @return unknown
     */
    function move($i, $j)
    {
        $i = $this->normalizeOffset($i);
        $j = $this->normalizeOffset($i);
        $this->assertOffset($i);
        $this->assertOffset($i);
        
        $this[$j] = $this[$i];
        return $this;
    }
    
    /**
     * リストの添え字で指定した要素どうしを交換し、自身を返す
     *
     * @param int $i
     * @param int $j
     * @return Sequence
     */
    function swap($i, $j)
    {
        $i = $this->normalizeOffset($i);
        $j = $this->normalizeOffset($i);
        $this->assertOffset($i);
        $this->assertOffset($i);
        
        $buf = $this[$i];
        $this[$i] = $this[$j];
        $this[$j] = $buf;
        return $this;
    }

    /**
     * リストの最初の要素を抜き出して末尾に挿入し、自身を返す
     *
     * @return Sequence
     */
    function roll()
    {
        return $this->push($this->shift());
    }
    
    /**
     * リストの最後の要素を抜き出して最初に挿入し、自身を返す
     *
     * @return Sequence
     */
    function rollback()
    {
        return $this->unshift($this->pop());
    }
    
    /**
     * 与えられた要素の添え字を格納したリストを生成する。
     *
     * @return Sequence
     */
    function indices()
    {
        $args = func_get_args();
        $ret = seq();
        foreach ($this as $i => $elt) if (in_array($elt, $args, true)) {
            $ret->push($i);
        }
        return $ret;
    }
    
    /**
     * 与えられた要素をリストの全ての要素の間に挟み込んだリストを生成する。
     * 
     * 例:
     * <code>
     * seq(1, 2, 3)->interleave(0); // => seq(1, 0, 2, 0, 3);
     * </code>
     *
     * @param unknown_type $elt
     * @return Sequence
     */
    function interleave($elt)
    {
        if ($this->count() <= 1) return seq();
        $ret = clone $this;
        return $ret->zip(seq()->lengthen($this->count() - 1)->fill($elt));
    }
    
    /**
     * 平らにしたリストを生成する
     *
     * @return Sequence
     */
    function flatten()    
    {
        return toseq($this->flattenInternally($this));
    }

    protected function flattenInternally(Sequence $seq, Array $buf = array())
    {
        foreach ($seq as $elt) if ($elt instanceof self){
           $buf = $this->flattenWithArray($elt, $buf);
        }
        else {
            $buf[] = $elt;
        }
        return $buf;
    }
}
