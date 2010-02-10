<?php
////////////////////////////////////////////////////////////////////////////////
// モジュール名 : 線を描画するためのクラス
// Production   : Okushin System Ltd.
////////////////////////////////////////////////////////////////////////////////
class pdfLine extends pdfWidget {
    private $_width;
    private $_color;
    
	// コンストラクタ 
	//  引数 : $wName 部品名
    function pdfLine($wName) {
        $this->_wName = $wName;
        $this->_visible = false;
        $this->_wType = 'line';
    }
    
    // プロパティをセットする
    //  引数 : $x1  	左上のX座標
    //         $y1  	左上のY座標
    //         $x2  	右下のX座標
    //         $y2  	右下のY座標
    //         $width   線の幅
    //         $color   描画色
    function setProperty($x1, $y1, $x2, $y2, $color, $width) {
        $this->_x1 = $x1;                 // 左上のX座標
        $this->_y1 = $y1;                 // 左上のY座標
        $this->_x2 = $x2;                 // 右下のX座標
        $this->_y2 = $y2;                 // 右下のY座標
        $this->_width = $width;       // 線の幅
        $this->_color = $color;         // 線の色
    }
    
	// 部品を描画する
    function Draw() {
        if (!$this->_visible) {
            return;
        }
        $this->_pdf->setPageFormat('A4', 'P');
        $this->_pdf->SetLineWidth($this->_width);
        $this->setDrawColor($this->_color);
        $this->_pdf->Line($this->_x1, $this->_y1,
                $this->_x2, $this->_y2);
    }
}
?>
