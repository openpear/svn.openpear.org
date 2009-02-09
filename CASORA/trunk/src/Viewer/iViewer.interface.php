<?php

interface iViewer
{

    public function assign( $key, $value );

    public function assignList( array $list );

    public function display( $template );
}
