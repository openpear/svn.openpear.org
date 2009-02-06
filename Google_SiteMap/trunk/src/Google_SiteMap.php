<?php
class GoogleSiteMap{
  public $sitemap_files = 1;
  public $num_of_urls=0;
  public $BASE_URL;
  //sitemapXX.xmlを作成。
  function render_site_xml($buffer) {
    $this->num_of_urls=0;
    $filename = "sitemap".$this->sitemap_files++.".xml";
    file_put_contents( $filename, '<?xml version="1.0" encoding="UTF-8" ?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$buffer.'</urlset>');
    file_put_contents("php://stderr","render_site_xml:{$filename}を出力\n");
    return "";
  }
  function add_url_element($url){
    $this->num_of_urls++;
    print '<url>'.
      '<loc>'."{$this->BASE_URL}".$url.'</loc>'.
      '<priority>0.5</priority>'.
      '<changefreq>monthly</changefreq>'.
      '<lastmod>'.date('c').'</lastmod>'.
    '</url>'."\n";
    if($this->num_of_urls>0 && $this->num_of_urls%50000 === 0){
      file_put_contents("php://stderr",$this->num_of_urls."を処理中\n");
      ob_flush();
    }
  }
  function sitemap_index(){
    global $sitemap_files;
    ob_start();
    echo '<?xml version="1.0" encoding="UTF-8"?>'.
      '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ( range(1 , $sitemap_files-1)  as $idx ){
          echo '<sitemap>'.
          '<loc>.'.$this->BASE_URL.'/sitemap'. $idx .'.xml</loc>'.
          '</sitemap>';
        }
    echo '</sitemapindex>';
    file_put_contents("sitemap.xml",ob_get_contents() );
    ob_end_clean();
  }
  public function init(){
    ob_start(array( $this,"render_site_xml"), 1024*1024*9.9 );
  }
  public function finish(){
    ob_end_flush();
    //最後にINDEXを書き出す。
    $this->sitemap_index();
    //出力済みをチェックする
    //チェックをする。
    file_put_contents("php://stdout", "作成したファイルのチェックを開始します\n");
    foreach( range(1,$this->sitemap_files-1) as $a ) {
      $filename = "sitemap".$a.".xml";
      $this->check_site_map($filename);
    }
  }
  //チェック関数。
  function check_site_map( $filename ){
    //ファイル読み込む
    $xml = simplexml_load_file($filename);
    file_put_contents("php://stdout", "{$filename}:\t<URL>".sizeof($xml->url)."件\t<filesize>".(filesize($filename)/1024/1024)."MB\n");
    //五万件未満
    if( sizeof($xml->url) > 50000 ){
      file_put_contents("php://stdout", "{$filename}：URLの件数超過です\n");
    }
    //サイズチェック
    if( (filesize( $filename ) ) >= (1024 * 1024 *10 )){
      file_put_contents("php://stdout", "{$filename}：URLの件数超過です\n");
    
    }
    //
    file_put_contents("php://stdout", "{$filename}：特にエラーは見つかりませんでした。\n");
  }
}