<?php

/**
 * ページガイド生成クラス
 *
 * リスト表示のページングリンクを生成する
 *
 * input
 *   Separate
 *   ViewMax
 *   LinkMax
 *   NowPage
 *   PageName
 *   BaseLink
 *   Amount
 *   NoLinkView
 * output
 *   page_link
 *   limit_sql
 *   view_from
 *   view_to
 *
 * 例
 * <code>
 * PageGuide::setParameter( array(
 *     'ViewMax' => '5',
 *     'LinkMax' => '10',
 *     'BaseLink' => '/member/ticket/history.php',
 *     'Amount' => $count,
 * ) );
 * $pg = PageGuide::getInstance();
 * $page_guide = $pg->build();
 *
 * print $page_guide['page_link'];
 * </code>
 *
 **/
class Pager
{

    /**
     * リンク区切り文字
     * @var string
     **/
    private static $Separate = '&nbsp;';

    /**
     * １ページの表示件数
     * @var int
     **/
    private static $ViewMax = 20;

    /**
     * ページリンク表示件数
     * @var int
     **/
    private static $LinkMax = 5;

    /**
     * 現在表示しているページ
     * @var int
     **/
    private static $NowPage = "";

    /**
     * GETで持ちまわす引数名
     * @var string
     **/
    private static $PageName = 'page';

    /**
     * ページングする基本URL
     * @var string
     **/
    private static $BaseLink = "";

    /**
     * 全件数
     * @var int
     **/
    private static $Amount = "";

    /**
     * ページリンク生成フラグ
     * @var int
     **/
    private static $NoLinkView = false;

    /**
     * フォーマット
     **/
    private static $FromFormat = '<span class="prev">{ANCHOR}</span>';
    private static $FromAnchor = '<strong>&laquo;前の{VIEW_MAX}件</strong>';

    /**
     * フォーマット
     **/
    private static $BetweenFormat = '<span class="{CURSOR}">{ANCHOR}</span>';
    private static $BetweenCursorDefault = 'blk';
    private static $BetweenCursorCheck   = 'cur';

    /**
     * フォーマット
     **/
    private static $ToFormat = '<span class="next">{ANCHOR}</span>';
    private static $ToAnchor = '<strong>次の{VIEW_MAX}件&raquo;</strong>';

    /**
     * シングルトンオブジェクトを格納する変数
     **/
    private static $singleton = null;

    public static function setParameter( array $param ){

        foreach( $param as $key => $val ){
            if( property_exists( get_class(), $key ) ) {
                self::$$key = $val;
            }
        }

    }

    /**
     * インスタンスを生成
     * 
     * 
     * @access public
     * @return object
     **/
    public static function getInstance() {

        if ( self::$singleton == null ) {
            $class_name = get_class();
            self::$singleton = new $class_name();
        }

        return self::$singleton;
    }

    public function build(){

        $page_area   = floor(self::$LinkMax / 2);
        $page_amount = ceil(self::$Amount / self::$ViewMax);
        $guide = array();

        // 表示ページ
        if( ! is_numeric(self::$NowPage) )          self::$NowPage = 1;
        else{
            if( self::$NowPage < 1 )                self::$NowPage = 1;
            elseif( self::$NowPage > $page_amount ) self::$NowPage = $page_amount;
        }

        // 表示件
        $guide['view_from'] = 1 + (self::$NowPage - 1) * self::$ViewMax;
        if( self::$NowPage * self::$ViewMax > self::$Amount ) $guide['view_to'] = self::$Amount;
        else                                                  $guide['view_to'] = self::$NowPage * self::$ViewMax;

        // limit句
        $guide['limit_sql'] = 'LIMIT ' . (self::$NowPage - 1) * self::$ViewMax .','. self::$ViewMax;

        // ガイド生成
        if( $page_amount >= 1 ){
            if( $page_amount > self::$LinkMax ){
                $page_start = self::$NowPage - $page_area;
                $page_end   = self::$NowPage + $page_area;
                if( $page_start < 1 ){
                    $page_start = 1;
                    $page_end   = self::$LinkMax;
                }elseif( $page_end > $page_amount ){
                    $page_start = 1 + $page_amount - self::$LinkMax;
                    $page_end   = $page_amount;
                }
            }else{
                $page_start = 1;
                $page_end   = $page_amount;
            }

            $guide['page_link'] = $this->makeHTML( $page_start, $page_end );
        }

        $guide['amount'] = self::$Amount;

        return $guide;
    }

    public function makeHTML( $page_start, $page_end ){

        $html = array();

        if ( $page_start == $page_end && ! self::$NoLinkView ) {
            ;
        } else {

            $FromAnchor = str_replace( '{VIEW_MAX}', self::$ViewMax, self::$FromAnchor );
            $ToAnchor   = str_replace( '{VIEW_MAX}', self::$ViewMax, self::$ToAnchor   );

            $uri = parse_url( self::$BaseLink );
            if ( isset($uri['query']) ) $base_link = self::$BaseLink .'&'. self::$PageName;
            else                        $base_link = self::$BaseLink .'?'. self::$PageName;

            $between = array();
            // 中間リンク生成
            for( $i = $page_start; $i <= $page_end; $i++ ){
                // 現在表示中のページリンク
                if ($i == self::$NowPage) {
                    $format = str_replace( '{CURSOR}', self::$BetweenCursorCheck, self::$BetweenFormat );
                    $between[] = str_replace( '{ANCHOR}', $i, $format );
                // 表示外のページリンク
                } else {
                    $anchor = '<a href="'. $base_link .'='. $i ."\">$i</a>";
                    $format = str_replace( '{CURSOR}', self::$BetweenCursorDefault, self::$BetweenFormat );
                    $between[] = str_replace( '{ANCHOR}', $anchor, $format );
                }
            }

            // 前ページリンク生成
            if ( self::$NowPage == 1 ) {
                $html[] = str_replace( '{ANCHOR}', $FromAnchor, self::$FromFormat );
            } else {
                $anchor = '<a href="'. $base_link .'='. (self::$NowPage - 1) .'">'. $FromAnchor .'</a>';
                $html[] = str_replace( '{ANCHOR}', $anchor, self::$FromFormat );
            }

            $html[] = implode( self::$Separate, $between );

            // 次ページリンク生成
            if (self::$NowPage == $page_end) {
                $html[] = str_replace( '{ANCHOR}', $ToAnchor, self::$ToFormat );
            } else {
                $anchor = '<a href="'. $base_link .'='. (self::$NowPage + 1) .'">'. $ToAnchor .'</a>';
                $html[] = str_replace( '{ANCHOR}', $anchor, self::$ToFormat );
            }

        }

        return implode( self::$Separate, $html );
    }
}
?>
