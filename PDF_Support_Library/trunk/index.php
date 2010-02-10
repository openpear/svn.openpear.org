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

// 背景PDF指定関数
// 第1引数：背景として使用するPDFファイルのパス
// 第2引数：用紙方向 P=縦長、L=横長
// 第3引数：用紙サイズ
$pdfObject->setBgPDF('parts/example.pdf', 'P', 'A4');

// CSVファイル読み込み
// 引数：CSVファイルのパス
$pdfObject->readFile('parts/example.csv');

// テキスト部品の文字列をセットする
// 第1引数：対象となるテキスト部品の名前
// 第2引数：セットする値
$pdfObject->setWidgetString('example1', "テスト文字列出力");

// 部品の表示/非表示プロパティをセットする
// 第1引数：対象となる部品の名前
// 第2引数：表示するか　true=表示する,false=表示しない
$pdfObject->setWidgetVisible('example2', true);
$pdfObject->setWidgetVisible('example3', true);
$pdfObject->setWidgetVisible('example4', true);

// 登録されている部品を描画する
$pdfObject->Draw();

// PDFを表示する
$pdfObject->showPDF();
?>