<?php

require_once( LIB_DIR. '/FW/Viewer/iViewer.interface.php' );

class Viewer
{

    private function __construct() {}

    public static function getInstance( $type )
    {

        $filename = LIB_DIR . '/FW/Viewer/'. strtolower($type) .'.class.php';

        if ( is_readable( $filename ) ) {
            require_once( $filename );
        } else {
            throw new Exception("Nothing viewer type. ($filename)");
        }
        
        $classname = 'Viewer_'. strtolower($type);

        return new $classname();
    }

    public function execute( $template, $path, $action, array $assign_list )
    {

        // テンプレート表示をする場合
        if( $template === false ) {
            // 表示しない

        } else {

            // テンプレートの指定がある場合
            if( $template !== null ) {
                // パスがある場合は、テンプレートパスを起点にする。.html縛りにしない
                if( strpos( $template, '/' ) !== false ) {
                    $file = "$template";
                // テンプレート名のみの場合は、コントロールパスと同じディレクトリから
                } else {
                    $file = "$path/$template.html";
                }
            // デフォルトはアクション名のHTMLファイル
            } else {
                $file = "$path/$action.html";
            }

            // テンプレートの存在チェック
            if( !is_readable( VIEWS_DIR . $file ) ){
                // テンプレートファイルが無い
                throw new Exception( "View file '$file' is not found." );
            }

            $this->assignList( $assign_list );

            $this->display( $file );
        }
        
    }

}
