<?php
////////////////////////////////////////////////////////////////////////////////
// モジュール名 : 文字列を描画するためのクラス
// Production   : Okushin System Ltd.
////////////////////////////////////////////////////////////////////////////////
class pdfText extends pdfWidget {
    var $_w;
    var $_h;
    var $_textColor;
    var $_textSize;
    var $_row;
    var $_align;
    var $_vAlign;
    var $_string;
    var $_frame;
    var $_bold;
    var $_italic;
    var $_under;
    
	// コンストラクタ 
	//  引数 : $wName 部品名
    function pdfText($wName) {
        $this->_wName = $wName;           // Widget名
        $this->_visible = false;
        $this->_wType = 'text';
        $this->_frame = 0;
    }

    // プロパティをセットする
    //  引数 : $x1  		左上のX座標
    //         $y1  		左上のY座標
    //         $x2  		右下のX座標
    //         $y2  		右下のY座標
    //         $color   	文字色
    //         $textSize   	フォントのサイズ
    //		   $row			最大行数
    //		   $align		水平方向の位置
    //		   $vAlign		垂直方向の位置
    //		   $bold		ボールド
    //		   $italic		イタリック
    //		   $under		アンダーライン
    function setProperty($x1, $y1, $x2, $y2, $textColor, $textSize, $row, $align, $vAlign, $bold=0, $italic=0, $under=0) {
        $this->_x1 = $x1;                 // 左上のX座標
        $this->_y1 = $y1;                 // 左上のY座標
        $this->_x2 = $x2;                 // 右下のX座標
        $this->_y2 = $y2;                 // 右下のY座標
        $this->_w = $x2 - $x1;            // 幅
        $this->_h = $y2 - $y1;            // 高さ
        $this->_textColor = $textColor;   // テキストの色
        $this->_textSize = $textSize;     // フォントのサイズ
        $this->_row = $row;               // 最大行数
        $this->_align = $align;           // 水平方向の位置
        $this->_vAlign = $vAlign;         // 垂直方向の位置
        $this->_bold = $bold;             // ボールド
        $this->_italic = $italic;         // イタリック
        $this->_under = $under;           // アンダーライン
    }

	// 文字列をセットする
	//  引数 : $val セットする文字列
    function setString($val) {
        $this->_string = $val;
        $this->_visible = true;
    }

	// 部品を描画する
    function Draw() {
        if (!$this->_visible) {
            return;
        }
        
        $style = "";
        //ボールドの場合、太文字にしておく
        if ($this->_bold == 1){
        	$style .= "B";
        }
        //イタリックの場合、斜体にしておく
        if ($this->_italic == 1){
        	$style .= "I";
        }
        //アンダーラインの場合、下線にしておく
        if ($this->_under == 1){
        	$style .= "U";
        }
        
        $this->_pdf->SetFont(TM_FONT, $style, $this->_textSize);
		// 改行コードで行に分割する。
        $rowStringArr = explode("\n", $this->_string, $this->_row);
        if (count($rowStringArr) <= 1) {
       	    $rowStringArr = array();
        	// 自動で改行の場合
        	if ($this->_pdf->GetStringWidth($this->_string) > $this->_w * $this->_row) {
        	    // スペースより文字列が長い時
        	    $rowWidth = $this->_pdf->GetStringWidth($this->_string) / $this->_row;
        	} else {
        	    // スペースより文字列が短い時
        	    $rowWidth = $this->_w;
        	}

    	    $j = 0;
            $rowStringArr[$j] = '';
    	    for ($i = 0; $i < strlen($this->_string); $i++) {
    	    	$length = $this->_pdf->getStringWidth($rowStringArr[$j]);
    	        if ($length >= $rowWidth) {
    	            $j++;
    	            $rowStringArr[$j] = '';
    	        }
    	        $rowStringArr[$j] .= $this->_string{$i};
    	    }
        } else {
        	// 明示的改行の場合
        	$maxStringrowNum = 1;
        	$last = array_pop($rowStringArr);
		    $rowStringArr[] =  strtr($last, "\n", ' '); // 最終行に残っている"\n"はスペースに変える
        }
        
        // 垂直方向の調整値を求める
        switch ($this->_vAlign) {
            case "C":  // 垂直方向:中央に配置
                $yAdjust = ($this->_row - count($rowStringArr)) * $this->_h / $this->_row / 2;
        		break;
            case "B":  // 垂直方向:下に配置
                $yAdjust = ($this->_row - count($rowStringArr)) * $this->_h / $this->_row;
                break;
            case "T":  // 垂直方向:上に配置
            default:
                $yAdjust = 0;
                break;
        }

        // 各行の左上Y座標を求める。
        $rowHeight = $this->_h / $this->_row;
        for ($i = 0; $i < $this->_row; $i++) {
            $rowY[$i] = $this->_y1 + $yAdjust + ($rowHeight * $i);  
        }
        
        $this->_pdf->SetXY($this->_x1, $this->_y1);
        $this->setTextColor($this->_textColor);
        $this->_pdf->SetLineWidth(0.1);
        
        // Debugモードの時の処理
        if ((TM_DBG_FRAME != 0) || (TM_DBG_FILL != 0)) {
            switch ($this->_widgetNo % 5) {
                case 0: // ピンク
                    $this->_pdf->SetFillColor(255, 204, 255);
                    $this->_pdf->SetDrawColor(255, 102, 255);
                    break;
                case 1: // 黄色
                    $this->_pdf->SetFillColor(255,255,153);
                    $this->_pdf->SetDrawColor(204,153,102);
                    break;
                case 2: // 青
                    $this->_pdf->SetFillColor(204,255,255);
                    $this->_pdf->SetDrawColor(102,153,204);
                    break;
                case 3: // オレンジ
                    $this->_pdf->SetFillColor(255,255,204);
                    $this->_pdf->SetDrawColor(255,204,102);
                    break;
                case 4: // 緑
                    $this->_pdf->SetFillColor(204,255,204);
                    $this->_pdf->SetDrawColor(51,153,102);
                    break;
            }
            $frame = TM_DBG_FRAME;
        } else {
            $frame = $this->_frame;
        }
        
		if ($this->_row > 1) {
		    // 枠描画指定時の枠とデバッグ用枠&塗り潰しを描画
		    if ($frame != 0) {
		        if (TM_DBG_FILL) {
                    $this->_pdf->Rect($this->_x1, $this->_y1, $this->_w, $this->_h, FD);
		        } else {
                    $this->_pdf->Rect($this->_x1, $this->_y1, $this->_w, $this->_h, D);
		        }
		    } else {
		        if (TM_DBG_FILL) {
                    $this->_pdf->Rect($this->_x1, $this->_y1, $this->_w, $this->_h, F);
		        }
		    }
		    // 文字を描画
		    for ($i = 0; $i < count($rowStringArr); $i++) {
		        $this->_pdf->SetXY($this->_x1,$rowY[$i]);
    		    $this->_pdf->Cell($this->_w, $rowHeight, $rowStringArr[$i], 0, 0, $this->_align, 0, '', 1);
		    }
		} else {
		    $this->_pdf->Cell($this->_w, $this->_h, $this->_string, $frame, 0, $this->_align, TM_DBG_FILL, '', 1);
		}
	}
}
?>
