<?php


	require_once 'Services/Shitaraba/Exception.php';
	require_once('Zend/Http/Client.php');
	require_once('Zend/Http/Cookie.php');

class Services_Shitaraba{
	
	//BBS名デフォルト
	const bbsModelDefault = 'Shitaraba';
	
	//エラーコード
    const ERROR_SERIVCES = 101;
    const ERROR_MODEL = 102;
	
	//コンストラクタ使用不可
    public function __construct(){
            throw new Services_Shitaraba_Exception(
                ' Services_Shitaraba::__construct OUT OF SERVICE. Please use Services_Shitaraba::factory ', self::ERROR_SERIVCES
            );
	}
	
    /**
    * ファクトリメソッド
    * @param string $bbsGenre     
    * @param string $bbsNum       
    * @param string $bbsPassword  
    * @param string $bbsModel     したらば以外のBBS
    *
    * @return Services_Blogging_Driver The driver object if all goes ok
    *
    * @throws Services_Blogging_Exception If an error occured
    */
    public static function factory($bbsGenre = false, $bbsNum = false, $bbsPassword = false, $bbsModel = self::bbsModelDefault){
		
		//モデルの存在チェック
        include_once 'Services/Shitaraba/Model/' . $bbsModel . '.php';
        
		$strClass = 'Services_Shitaraba_Model_' . $bbsModel;
        if (!class_exists($strClass)) {
            throw new Services_Shitaraba_Exception(
                'Invalid model "' . $bbsModel . '" specified!', self::ERROR_MODEL
            );
        }
		
        if (!is_subclass_of($strClass, 'Services_Shitaraba_Model_Abstract')) {
            throw new Services_Shitaraba_Exception(
                'Model class ' . $strClass . ' is not a child class'
                . ' of Services_Shitaraba_Model_Abstruct', self::ERROR_MODEL
            );
        }
		//インスタンスにして返す
        $class = new $strClass($bbsGenre, $bbsNum, $bbsPassword);
        return $class;
		
	}
	
	
}