<?php
////////////////////////////////////////////////////////////////////////////////
// モジュール名 : PDF出力管理クラス
// Production   : Okushin System Ltd.
////////////////////////////////////////////////////////////////////////////////
require_once('pdfWidget.php');
require_once('pdfText.php');
require_once('pdfLine.php');
require_once('pdfRect.php');
require_once('pdfImage.php');

class pdfLibrary {
    private $_name;
    private $_pdf;

    private $_author = NULL;
    private $_title = NULL;
    private $_subject = NULL;

	// コンストラクタ 
	// 第1引数：$name 管理クラスの名前(セッション名として使用する)
	// 第2引数：$font フォント
	// 第3引数：$grid デバッグ用グリッド出力
	// 第4引数：$frame デバッグ用枠出力
	// 第5引数：$fill デバッグ用塗りつぶし
    function pdfLibrary($name, $font='kozgopromedium', $grid = false, $frame = 0, $fill = 0) {
        define('TM_FONT', $font);
        define('TM_DBG_GRID', $grid);
        define('TM_DBG_FRAME', $frame);
        define('TM_DBG_FILL', $fill);

        $this->_name = $name;
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
        $this->_pdf = new FPDI();
    }

	// 背景PDF指定関数
	// 第1引数：$file 背景として使用するPDFファイルのパス
	// 第2引数：$orientation 用紙方向 P=縦長、L=横長
	// 第3引数：$format 用紙サイズ
    function setBgPDF($file, $orientation = 'P', $format = 'A4') {
        $this->_pdf->setPrintHeader(false);
        $this->_pdf->setPrintFooter(false);
        $this->_pdf->SetAutoPagebreak(false, 0);

        $pagecount = $this->_pdf->setSourceFile($file);
        $tplidx = $this->_pdf->ImportPage(1);

        $this->_pdf->AddPage($orientation, $format);
        $this->_pdf->useTemplate($tplidx);

        $this->_drawDbgGrid(TM_DBG_GRID, $orientation, $format);

        $this->_pdf->SetDrawColor(255,100,0);
        $this->_pdf->SetFont("kozgopromedium", "", 10);
    }

	// 背景なしPDF指定関数
	// 第1引数：$orientation 用紙方向 P=縦長、L=横長
	// 第2引数：$format 用紙サイズ
    function addPage($orientation = 'P', $format = 'A4') {
        $this->_pdf->setPrintHeader(false);
        $this->_pdf->setPrintFooter(false);
        $this->_pdf->SetAutoPagebreak(false, 0);

        $this->_pdf->AddPage($orientation, $format);

        $this->_drawDbgGrid(TM_DBG_GRID, $orientation, $format);

        $this->_pdf->SetDrawColor(255,100,0);
        $this->_pdf->SetFont("kozgopromedium", "", 10);
    }
    
    function setProperty($author, $title, $subject) {
        $this->_author = $author;
        $this->_title = $title;
        $this->_subject = $subject;
    }

    // グリッドの線を描画する
    // 第1引数：$dbgFlg 描画するかしないか true=描画する、false=描画しない
	// 第2引数：$orientation  用紙方向 P=縦長、L=横長
	// 第3引数：$format 用紙サイズ
    function _drawDbgGrid($dbgFlg, $orientation, $format) {
    	if ($dbgFlg == false) {
    		return;
    	}
    	
    	//フォーマットの種類からサイズを求める
    	switch($format){
    		case 'B3':
   				$short = 350;
   				$long = 500;
    			break;
    		case 'B4':
   				$short = 250;
   				$long = 350;
    			break;
    		case 'B5':
   				$short = 175;
   				$long = 250;
    			break;
    		case 'A3':
   				$short = 297;
   				$long = 420;
    			break;
    		case 'A5':
   				$short = 148;
   				$long = 210;
    			break;
    		case 'A4':
    		default:
   				$short = 210;
   				$long = 297;
    	}
    	
    	//L=横長かP=縦長で横幅・縦幅の長さを決める
    	if ($orientation == 'P'){
    		$width = $short;
    		$height = $long;
    	}else{
    		$width = $long;
    		$height = $short;
    	}
    	
        $this->_pdf->SetFont("kozgopromedium", "", 8);
        $this->_pdf->SetTextColor(102,204,255);
        $this->_pdf->SetDrawColor(204,255,255);
        $this->_pdf->SetLineWidth(0.4);
        for ($i = 1; $i < 100; $i++) {
            $x = $i*10;
            $this->_pdf->SetXY(0,$x-7);
            $this->_pdf->Cell(5,10,$x);
            $this->_pdf->SetXY($width-5,$x-7);
            $this->_pdf->Cell(5,10,$x,0,0,'R');
            
            $this->_pdf->SetXY($x,0);
            $this->_pdf->Cell(5,5,$x);
            $this->_pdf->SetXY($x,$height-5);
            $this->_pdf->Cell(5,5,$x);
            
            $this->_pdf->Line($x,0,$x,$height);
            $this->_pdf->Line(0,$x,$width,$x);
        }
        $this->_pdf->SetLineWidth(0.1);
        for ($i = 1; $i < 100; $i++) {
            $x = $i*10 + 5;
            $this->_pdf->Line($x,0,$x,$height);
            $this->_pdf->Line(0,$x,$width,$x);
        }
    }

    function showPDF() {
        //ファイル情報
        $fileName = $this->_name . ".pdf";
        $this->_pdf->Output($fileName,"I");
    }
    
	// CSVファイル読み込み
	//  引数 : $fileName CSVファイルのパス
    function readFile($fileName) {
        $fp = fopen($fileName, "r") or die("$fileName couldn't open.");
        while (($property = fgetcsv($fp, 1000, ",")) !== false) {
            if ($property[0] == '') {
                continue;
            }
            
            $widget = null;
            switch ($property[1]) {
                case 'text':   
                    $widget = new pdfText($property[0]);
                    $widget->setProperty(
                            $property[3], $property[4], $property[5],
                            $property[6], $property[7], $property[8],
                            $property[9], $property[10], $property[11],
                            $property[12], $property[13], $property[14]);
                    break;
                case 'line':
                    $widget = new pdfLine($property[0]);
                    $widget->setProperty(
                            $property[3], $property[4], $property[5],
                            $property[6], $property[7], $property[8]);
                    break;
                case 'rect':
                    $widget = new pdfRect($property[0]);
                    $widget->setProperty(
                            $property[3], $property[4], $property[5],
                            $property[6], $property[7], $property[8]);
                    break;
                case 'image':
                    $widget = new pdfImage($property[0]);
                    $widget->setProperty(
                            $property[3], $property[4], $property[5],
                            $property[6], $property[7]);
                    break;
                default:
                    break;
            }
            if (!is_null($widget)) {
                $this->addNewWidget($widget);
            }
        }
        fclose($fp);
    }

	// 部品追加
	//  引数 : $widget 追加する部品
    function addNewWidget($widget) {
        $widget->setPdf($this->_pdf);
        if (isset($_SESSION[$this->_name][$widget->getWidgetName()])) {
            // 既に同じ名前が部品として登録されていたら上書きしてしまう
            unset ($_SESSION[$this->_name][$widget->getWidgetName()]);
        }
        if (isset($_SESSION[$this->_name])) {
        	$count = count($_SESSION[$this->_name]);
        } else {
        	$count = 0;
        }
        $widget->setWidgetNo($count);     
        $_SESSION[$this->_name][$widget->getWidgetName()] =& $widget;
    }
    
    // テキスト部品の文字列をセットする
	//  引数 : $widgetName 対象となるテキスト部品の名前
	//         $value      セットする値
    function setWidgetString($widgetName, $value) {
        if (isset($_SESSION[$this->_name][$widgetName])) {
            if ($_SESSION[$this->_name][$widgetName]->getWidgetType() == 'text') {
                $_SESSION[$this->_name][$widgetName]->setString($value);
            }else{
                $_SESSION[$this->_name][$widgetName]->setVisible();
            }
        }
    }
    
    // 部品の表示/非表示プロパティをセットする
	//  引数 : $widgetName 対象となる部品の名前
	//         $value      セットする値
    function setWidgetVisible($widgetName, $value) {
        if (isset($_SESSION[$this->_name][$widgetName])) {
            $_SESSION[$this->_name][$widgetName]->setVisible($value);
        }
    }

    // 登録されている部品を描画する
    function Draw() {
    	if (isset($_SESSION[$this->_name])) {
	        foreach($_SESSION[$this->_name] as $key => $value) {
	            $value->Draw();
	        }
    	}
        $_SESSION[$this->_name] = array();
    }
}
?>
