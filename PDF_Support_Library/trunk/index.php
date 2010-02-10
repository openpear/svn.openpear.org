<?php
require_once('tcpdf/config/lang/jpn.php');
require_once('tcpdf/tcpdf.php');
require_once('FPDI/fpdi.php');
require_once('library/pdfLibrary.php');

// ライブラリクラスを読み込む
// 第1引数：セッションの名前として保存
// 第2引数：フォント
// 第3引数：グリッドを表示するか　true=表示する,false=表示しない
// 第4引数：デバッグ用枠出力　0=枠を非表示,1=枠を表示
// 第5引数：デバッグ用塗りつぶし　0=塗りつぶさない,1=塗りつぶす
$pdfObject = new pdfLibrary('samplePdf', 'kozgopromedium', true, 1, 1);

$pdfObject->setBgPDF('parts/example.pdf', 'P', 'A4');
$pdfObject->readFile('parts/example.csv');
$pdfObject->setWidgetString('example1', "テスト文字列出力");
$pdfObject->setWidgetVisible('example2', true);
$pdfObject->setWidgetVisible('example3', true);
$pdfObject->setWidgetVisible('example4', true);

$pdfObject->Draw();

// PDFを表示する
$pdfObject->showPDF();
?>