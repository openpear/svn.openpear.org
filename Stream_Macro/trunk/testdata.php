<?php
echo "** include Test **\r\n";
#include sample.txt
echo "\r\n-------------------------------------\r\n";

echo "** define Test **\r\n";
#define $hoge "hoge is foo"
echo "%%hoge%%";
echo "\r\n-------------------------------------\r\n";

echo "** define Test2 **\r\n";
#define sum($a,$b) ($a + $b) 
echo "%%sum(5,3)%%";
echo "\r\n-------------------------------------\r\n";

echo "** ifdef Test **\r\n";
$mssg = "John";
#ifdef $debug
#/	$mssg = "Sunnily";
#else
#/	$mssg = "Michael";
#endif
echo "Hello " . $mssg . "!!";
echo "\r\n-------------------------------------\r\n";

echo "** if Test **\r\n";
#if $debug == true
#/	echo "This is Debug Mode";
#endif
echo "\r\n-------------------------------------\r\n";

echo "** for Test **\r\n";
#for $p=0;$p<10;$p++
#/	echo "[%%p%%]";
#endfor
echo "\r\n-------------------------------------\r\n";

echo "** foreach Test **\r\n";
#define $data array('hoge'=>'foo', 'a'=>'b', 'c'=>'d')
#foreach $data as $key=>$val
#/	echo "%%key%% => %%val%%";
#/	echo "\r\n";
#endforeach
echo "\r\n-------------------------------------\r\n";

echo "** white Test **\r\n";
#define $now 0
#while 10>$now++
#/	echo "(%%now%%)";
#endwhile
echo "\r\n-------------------------------------\r\n";

