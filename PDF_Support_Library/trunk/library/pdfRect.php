<?php
////////////////////////////////////////////////////////////////////////////////
// モジュール名 : 四角を描画するためのクラス
// Production   : Okushin System Ltd.
////////////////////////////////////////////////////////////////////////////////
class pdfRect extends pdfWidget {
    private $_w;
    private $_h;
    private $_color;
    private $_paint;
    
	// コンストラクタ 
	//  引数 : $wName 部品名
    function pdfRect($wName) {
        $this->_wName = $wName;
        $this->_visible = false;
        $this->_wType = 'rect';
    }
    
    // プロパティをセットする
    //  引数 : $x1  	左上のX座標
    //         $y1  	左上のY座標
    //         $x2  	右下のX座標
    //         $y2  	右下のY座標
    //         $color   描画色
    function setProperty($x1, $y1, $x2, $y2, $color, $paint=1) {
        $this->_x1 = $x1;                 // 左上のX座標
        $this->_y1 = $y1;                 // 左上のY座標
        $this->_x2 = $x2;                 // 右下のX座標
        $this->_y2 = $y2;                 // 右下のY座標
        $this->_w = $x2 - $x1;            // 幅
        $this->_h = $y2 - $y1;            // 高さ
        $this->_paint = $paint;           // 塗りつぶすかどうか0=塗りつぶさない、1=塗りつぶす
        $this->_color = $color;
    }
    
    // 部品を描画する
    function Draw() {
        if (!$this->_visible) {
            return;
        }
        $this->_pdf->SetLineWidth(0.1);
        $this->setDrawColor($this->_color);
        $this->setFillColor($this->_color);
        if ($this->_paint == 1){
	        $this->_pdf->Rect($this->_x1, $this->_y1, $this->_w, $this->_h, 'F');
        }else{
	        $this->_pdf->Rect($this->_x1, $this->_y1, $this->_w, $this->_h);
        }
    }
}
?>
