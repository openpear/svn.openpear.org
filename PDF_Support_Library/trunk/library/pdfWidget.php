<?php
////////////////////////////////////////////////////////////////////////////////
// モジュール名 : 全ての部品の基底クラス
// Production   : Okushin System Ltd.
////////////////////////////////////////////////////////////////////////////////
class pdfWidget {
    var $_widgetNo;
    var $_wName;
    var $_wType;
    var $_x1;
    var $_y1;
    var $_x2;
    var $_y2;
    var $_pdf;
    var $_visible;
    
    // 部品名を取得する
	//  戻り値 : 部品名
    function getWidgetName() {
        return $this->_wName;
    }

    // 部品種別を取得する
	//  戻り値 : 部品種別
    function getWidgetType() {
        return $this->_wType;
    }
    
    // PDFクラスのインスタンスをセットする
    function setPdf(&$pdf) {
        $this->_pdf =& $pdf;
    }
    
    // 部品番号を取得する(デバッグ用にしか使っていない)
	//  戻り値 : 部品番号
    function setWidgetNo($no) {
        $this->_widgetNo = $no;
    }
    
    // 部品を出力するか否かのプロパティをセットする
    //  引数 : $value true 表示 / false 表示しない 
    function setVisible($value) {
        $this->_visible = $value;
    }

	// テキストの色をセットする
    //  引数 : $color #ffffffの形式で来る
    function setTextColor($color) {
        $result = $this->convertRGB($color);
        $this->_pdf->SetTextColor($result[0], $result[1], $result[2]);
    }
    
	// 描画色(外枠)をセットする
    //  引数 : $color #ffffffの形式で来る
    function setDrawColor($color) {
        $result = $this->convertRGB($color);
        $this->_pdf->SetDrawColor($result[0], $result[1], $result[2]);
    }
    
	// 描画色(内枠)をセットする
    //  引数 : $color #ffffffの形式で来る
    function setFillColor($color) {
        $result = $this->convertRGB($color);
        $this->_pdf->SetFillColor($result[0], $result[1], $result[2]);
    }
    
    //カラー表示を16進数表示から10進数表示にして返す
    //  引数 : $color 16進数のカラー表示
    //  戻り値 : 10進数のカラー表示(配列形式)
    function convertRGB($color){
		$color = preg_replace("/^#/", '', $color);
		
		$result = array();
		for($i = 0; $i < 6; $i+=2) {
			$hex = substr($color, $i, 2);
			$result[] = hexdec($hex);
		}
		
		return $result;
    }
}
?>
