<?php

class Viewer_default extends Viewer implements iViewer
{
    private $assign = array();

    public function __construct()
    {
        //キャッシュ機能の有効化
        //$smarty->caching = true;
    }

    public function assign( $key, $value )
    {
        $this->assign[$key] = $value;
    }

    public function assignList( array $list )
    {
        foreach ( $list as $key => $value ) {
            $this->assign( $key, $value );
        }
    }

    public function display( $template )
    {
        foreach ( $this->assign as $key => $value ) {
            $$key = $value;
        }
        //テンプレートを指定し表示
        include( VIEWS_DIR.$template );
    }
}
