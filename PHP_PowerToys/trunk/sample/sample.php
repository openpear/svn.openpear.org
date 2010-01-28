<?php
require_once('../PowerToys.php');
$pt = new PHP_Powertoys();

//PEAR系
//$obj = $pt->getPearI('Net_POP3');
//$obj = $pt->getPear('Net/POP3.php');

//変数/配列系
//$a['a'] = 'a';
//$a['b'] = 'b';
//$a['c'] = 'c';

//$a = $pt->arrayKeyReplace($a, 'b', 'd');
//実際に保存されちゃうのでコメントアウト
//$pt->save_var_dump($a, 'var_dump.txt');
//$pt->print_r_ex($a);

//UTF-8系
//$bom = $pt->checkBom('sample.txt');
//if($bom){
//	echo 'BOM付きです';
//}

//$str = file_get_contents('sample.txt');
//$str = $pt->removeBom($str);
//$bom = $pt->checkBom($str);
//if(!$bom){
//	echo 'BOMなしです';
//}

//$str = file_get_contents('nobom.txt');
//$str = $pt->addBom($str);
//$bom = $pt->checkBom($str);
//if($bom){
//	echo 'BOMありです';
//}

//画像系
//$img = $pt->iopen(dirname(dirname(__FILE__)) . '/sample/sample.jpg');
//$img = $pt->iopen(dirname(dirname(__FILE__)) . '/sample/sample.png');
//$img = $pt->iopen(dirname(dirname(__FILE__)) . '/sample/sample.gif');
//$img = $pt->iopen(dirname(dirname(__FILE__)) . '/sample/sample.bmp');

//オブジェクト系
//$pt2 = $pt->objectClone($pt);

//ファイル取得
//echo $pt2->file_get_contents_ex('http://openpear.org/');

//$array = array('abcdefg', 'abcd' , 'def', 'hij', 'klm');
//$result = $pt2->in_array_ex('.*d.*', $array);
//echo $result; //結果は恐らく3
//$ini = $pt2->iniParser('sample.ini');
//print_r($ini);

//16進数表示
//$data = $pt->hexFromFile('');
//echo $data;
//2進数表示
//$data = $pt->binFromFile('');
//echo $data;

//ガベージコレクション
//$pt->gerbageCollection(true);

//携帯向けHTML圧縮
//$pt = new PHP_Powertoys('compress');
//$pt = compressMobileHtml('debug');

//配列をDTOに変換
//$array = array('test' => 'php', 'test2' => 'perl');
//$dto = $pt->arrayToDto($array);
//echo $dto->get('test');

//今度はDTOを配列に変換(PHP_PowerToys基準のDTO)
//$array = $pt->dtoToArray($dto);
//print_r($array);

//モザイク
//$img = $pt->mosaic(dirname(dirname(__FILE__)) . '/sample/sample.jpg');
//imagejpeg($img, dirname(dirname(__FILE__)) . '/sample/mosaic.jpg');

//$array['test1']['test2']['test3'] = 'a';
//$count = $pt->getCountDimentionArrays($array);
//echo $count;
?>