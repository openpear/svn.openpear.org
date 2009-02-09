<?php

require_once(LIB_DIR.'/FW/Viewer/Smarty-2.6.18/libs/Smarty.class.php');

class Viewer_smarty extends Viewer implements iViewer
{
    public $instance;

    public function __construct()
    {
        $this->instance = new Smarty();

        //各ディレクトリの指定
        $this->instance->template_dir = VIEWER_SMARTY_TEMPLATES;
        $this->instance->compile_dir = VIEWER_SMARTY_TEMPLATES_C;
        $this->instance->config_dir = VIEWER_SMARTY_CONFIGS;
        $this->instance->cache_dir = VIEWER_SMARTY_CACHE;

        //
        $this->instance->left_delimiter = VIEWER_SMARTY_LEFT_DELIMITER;
        $this->instance->right_delimiter = VIEWER_SMARTY_RIGHT_DELIMITER;
        //キャッシュ機能の有効化
        //$smarty->caching = true;
    }

    public function assign( $key, $value )
    {
        $this->instance->assign( $key, $value );
    }

    public function assignList( array $list )
    {
        $this->instance->assign( $list );
    }

    public function display( $template )
    {
        //テンプレートを指定し表示
        $template = preg_replace( '#^/#', '', $template );
        $this->instance->display( $template );
    }
}
