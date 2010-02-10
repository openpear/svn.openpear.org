<?php
////////////////////////////////////////////////////////////////////////////////
// モジュール名 : 画像を描画するためのクラス
// Production   : Okushin System Ltd.
////////////////////////////////////////////////////////////////////////////////
class pdfImage extends pdfWidget {
    private $_w;
    private $_h;
    private $_file;
    
	// コンストラクタ 
	//  引数 : $wName 部品名
    function pdfImage($wName) {
        $this->_wName = $wName;
        $this->_visible = false;
        $this->_wType = 'image';
    }
    
    // プロパティをセットする
    //  引数 : $x1  	左上のX座標
    //         $y1  	左上のY座標
    //         $x2  	右下のX座標
    //         $y2  	右下のY座標
    //         $file    画像ファイルのパス
    function setProperty($x1, $y1, $x2, $y2, $file) {
        $this->_x1 = $x1;                 // 左上のX座標
        $this->_y1 = $y1;                 // 左上のY座標
        $this->_x2 = $x2;                 // 右下のX座標
        $this->_y2 = $y2;                 // 右下のY座標
        $this->_w = $x2 - $x1;            // 幅
        $this->_h = $y2 - $y1;            // 高さ
        $this->_file = $file;        
    }
    
    // 部品を描画する
    function Draw() {
        if (!$this->_visible) {
            return;
        }
        $this->_pdf->Image($this->_file, $this->_x1, $this->_y1, $this->_w, $this->_h);
    }
}
?>
