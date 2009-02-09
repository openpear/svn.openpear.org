<?php
/**
 * コントローラー
 *
 * 
 *
 * 例
 * <code>
 *   require_once('FW/Controller.class.php');
 *
 *   Controller::execute();
 * </code>
 *
 * @version 0.1
 * @author $ryun$
 * @package lib
 * @copyright Copyright(c) RyuN Corp.
 **/
class Controller{

    /**
     * コンストラクタ
     * 
     * 
     * @access private
     * @return void
     **/
    private function __constract(){}

    /**
     * 
     * 
     * 
     * @access public
     * @return void
     **/
    public static function execute()
    {

        try{

            // url routing ------------
            $router = Router::getInstance( ROUTER_TYPE );
            $router->build();
            Logger::to()->debug('Get routing '. str_replace("\n", '', substr(print_r($router,true),0,512) ) .'. )');
            
            $model_file = MODELS_DIR. $router->path .'/'. ucfirst($router->action) .'.class.php';
            $view_file  = VIEWS_DIR.  $router->path .'/'. $router->action .'.html';

            // モデルファイルが無い場合、ビューがあれば読み込む
            if ( !is_readable( $model_file ) ) {
                if ( !is_readable($view_file) ) {
                    throw new Exception("Model($model_file) and view file($view_file) is not found.");
                } else {
                    // ビューの読み込み
                    require_once( $view_file );
                    Logger::to()->info("View file '$view_file' was load. )");
                    exit;
                }
            }

            // モデルの読み込み
            require_once( $model_file );
            Logger::to()->info("Model file '$model_file' was load. )");

            //$name = ucfirst( $router->application );
            $name = "";
            foreach( explode('/',$router->path) as $path ) $name .= ucfirst($path);
            $name .= ucfirst($router->action);

            if ( !class_exists( $name ) ) {
                // クラスが無い
                throw new Exception( "Not found '$name' class." );
            }

            // テスト実行
            if ( @$router->test_flag ) {
                $test = Test::getInstance( TEST_TYPE );
                $test->run( TEST_DIR, $router->path, $router->action );
                exit;
            }

            // インスタンス生成
            eval( "\$instance = new $name();" );
            Logger::to()->info("Make instance is '$name' class. )");

            // メンバ変数にリクエストを定義する
            foreach( $router->requests as $key => $val ){
                $val = mysql_escape_string( $val );
                $val = htmlspecialchars( $val, ENT_QUOTES );
                $instance->$key = $val;
            }

            // varidateメソッドがあればバリデーションする
            $template_name = method_exists( $instance, 'validate' ) ? $instance->validate( $router->requests ) : null;

            if( !is_scalar($template_name) ){

                if( !method_exists( $instance, 'action' ) ){
                    // アクションがない
                    Logger::to()->err( "Not found actioin method." );
                }

                // アクション実行
                $template_name = $instance->action( $router->requests );
            }

            $vars = get_object_vars($instance);

        }catch( Exception $e ){
            $timestamp = time();

            Logger::to()->err( "[$timestamp] ". $e->getMessage() );

            if ( $e->getCode() != 999 ) {
                if ( defined('ERROR_TEMPLATE') ){
                    $vars['message'] = $timestamp;

                    $template_name = '/'.ERROR_TEMPLATE;
                }

                trigger_error( $e->getMessage(), E_USER_ERROR );

            } else {
                $vars['message'] = $e->getMessage();
                $template_name = "/error.html";
            }
        }

        // Viewer -----------------
        ob_start();
        $viewer = Viewer::getInstance( VIEWER_TYPE );
        $viewer->execute( $template_name, $router->path, $router->action, $vars );
        $html = ob_get_clean();
        //Logger::to()->info("View file '$file' was load. )");

        // filterメソッドがあれば実行する
        if ( method_exists( $instance, 'filter' ) ) {
            $html = $instance->filter( $html );
        }

        print $html;

    }

}
?>
