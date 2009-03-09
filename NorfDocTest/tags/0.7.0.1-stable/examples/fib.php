<?php

/**
 * フィボナッチ数列を計算する。
 *
 * #test n = 0 の場合
 * >>> fib(0);
 * 0
 *
 * #test n = 1 の場合
 * >>> fib(1);
 * 1
 *
 * #test n = 10 の場合
 * >>> fib(10);
 * 55
 *
 * #test n < 0 であれば例外
 * >>> fib(-1);
 * InvalidArgumentException: n must be >= 0
 *
 */
function fib($n)
{
    if ($n < 0)
        throw new InvalidArgumentException('n must be >= 0');
    else if ($n == 0)
        return 0;
    else if ($n == 1)
        return 1;
    else
        return fib($n-1) + fib($n-2);
}

