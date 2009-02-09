<?
/**
 * XMLクラス
 *
 *
 * 例
 * <code>
 *   require_once('XML.class.php');
 *   //つくるとき
 *   XML::setParamater( array( 'Master' => array( DB_MASTER ),
 *                            'Slave'  => array( DB_SLAVE ),
 *                            'Type'   => DB_TYPE,
 *                            'Name'   => DB_NAME,
 *                            'User'   => DB_USER,
 *                            'Pass'   => DB_PASS ) );
 *
 *   $xml = XML::getInstance( );
 *   $feed = $xml->build( $data );
 *   // パースするとき
 *   $data = $xml->parse($feed);
 * </code>
 *
 * @version 0.1
 * @author $ryun$
 * @package lib
 * @copyright Copyright(c) RyuN Corp.
 **/
class XML
{

    private static $formatOutput = TRUE;

    private static $encoding = 'UTF-8';

    private static $rootElementName = 'root';

    private static $defaultTag = 'item';

    private static $singleton = null;


    public static function setParamater( array $param )
    {

        foreach( $param as $key => $val ){
            if( property_exists( get_class(), $key ) ) {
                if( ( $key == 'Master' || $key == 'Slave') && !is_array($val) ){
                    self::$$key = explode( ',', $val );
                } else {
                    self::$$key = $val;
                }
            }
        }

    }

    public static function getInstance( array $param = null ) {

        if ( XML::$singleton == null ) {
            XML::$singleton = new XML();

            if ( $param !== null ) self::setParamater( $param );
        }

        return XML::$singleton;
    }

    public function build( array $item = null, $output = false ){

        $instance = new domDocument;
        $instance->formatOutput = self::$formatOutput;
        $instance->encoding = self::$encoding;

        if( $item ){
            $root = $instance->appendChild( new domElement( self::$rootElementName ) );
            $this->setElement( $item, $root );
        }

        $xml = $instance->saveXML();

        if ( $output ) {
            header('Content-Type: text/xml; charset='. self::$encoding, false );
//            header("pragma: no-cache");
//            header("cache-control: no-cache");
//            header("expires: 0");
            print $xml;
        }else{
            return $xml;
        }
    }

    private function setElement( $item, &$element, $parent_name = null ){

        foreach ( $item as $name => $value ) {

            if ( is_int( $name ) ) {
                $name = $parent_name;
                //$name = self::$defaultTag;
            }

            if ( is_array($value) ) {
                if ( count( preg_grep( '/^[0-9]+$/', array_keys($value) ) ) == count($value) ) {
                    foreach( $value as $item ) $this->setElement( $item, $element->appendChild( new domElement($name) ), $name );
                } else {
                    $this->setElement( $value, $element->appendChild( new domElement($name) ), $name );
                }
            } else {
                if( is_object($value) ) {
                     $dom = $element->appendChild( new domElement($name, null) );
                     foreach( $value as $key => $val ) $dom->setAttribute( $key, $val );
                } else {
                     $element->appendChild( new domElement($name, $value) );
                }
            }
        }
    }

    public function parse( $xml ){
        return simplexml_load_string( $xml );
    }

    /**
     * XML生成用連想配列用の返り値にする
     *
     * @param 
     * @see
     * @access private
     * @return array
     **/
    public static function makeHash( $result, $code = "", $body = null ){

        return array( 'head' => array( 'result' => $result, 'code' => $code ), 'body' => $body );

    }

}
?>
