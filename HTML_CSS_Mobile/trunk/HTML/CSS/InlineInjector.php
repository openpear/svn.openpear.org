<?php

abstract class HTML_CSS_InlineInjector_Abstract
{
    protected function _preLoadDom()
    {}

    protected function _loadDom()
    {
        /****************************************
         * 本処理
         ****************************************/
        // libxmlのエラーをハンドリング
        libxml_use_internal_errors(true);

        // XHTMLをパース
        $this->dom = new DOMDocument();
        $this->dom->loadHTML($document);

        $this->dom_xpath = new DOMXPath($this->dom);

        $this->loadCSS();

        if (is_null($this->html_css))
        {
          return $original_document;
        }

        // CSSをインライン化
        $css = $this->html_css->toArray();
        $add_style = array();
        foreach ($css as $selector => $style)
        {
          // 疑似要素は退避。@ルールはスルー(Selector2XPath的にバグでやすい)
          if (strpos($selector, '@') !== false) continue;
          if (strpos($selector, ':') !== false)
          {
            $add_style[] = $selector . '{' . $this->html_css->toInline($selector) . '}';
            continue;
          }

          $xpath = HTML_CSS_Selector2XPath::toXPath($selector);
          $elements = $this->dom_xpath->query($xpath);

          if (!($elements instanceof DOMNodeList)) continue;
          if ($elements->length == 0) continue;
          // inlineにするCSS文を構成(toInline($selector)だとh2, h3 などでうまくいかない問題があったため)
          $inline_style = '';
          foreach ($style as $k => $v)
          {
            $inline_style .= $k . ':' . $v . ';';
          }
          foreach ($elements as $element)
          {
            if ($attr_style = $element->attributes->getNamedItem('style'))
            {
              // style要素が存在する場合は前方追記
              #TODO: できれば、重複回避もしたい。少しロジックがまどろっこしい順序になってしまうのだが。。。
              $attr_style->nodeValue = $inline_style . $attr_style->nodeValue;
            }
            else
            {
              // style要素が存在しない場合は追加
              $element->setAttribute('style', $inline_style);
            }
          }
        }

        // 疑似クラスを<style>タグとして追加
        if (!empty($add_style))
        {
          $new_style = implode(PHP_EOL, $add_style);
          $new_style = str_replace(']]>', ']]]><![CDATA[]>', $new_style);
          $new_style = implode(PHP_EOL, array('<![CDATA[', $new_style, ']]>'));

          $head = $this->dom_xpath->query('//head');
          $new_style_node = new DOMElement('style', $new_style);
          $head->item(0)->appendChild($new_style_node)->setAttribute('type', 'text/css');
        }

        $result = $this->dom->saveHTML();


        // libxmlのエラーを退避して削除
        $this->errors = libxml_get_errors();
        libxml_clear_errors();

    }

    public function apply($text)
    {
        $this->_preLoadDom();
        $this->_loadDom();

        $this->_loadCss();

        // CSSをインライン化
        if (!$this->getHTMLCSS()) {
        }
        
        $text = $this->_inject();

        $text = $this->_postInject($text);

        return $text;
    }

    protected function _postInject($body)
    {}

}

// HTML_CSS_Mobile
class HTML_CSS_InlineInjector extends HTML_CSS_InlineInjector_Abstract
{
    private $_backup = array();

    public static function applyHtml($text, $base_dir)
    {
        $self = new self;
        //$self->setConfig();
        $self->apply($text);
    }

    protected function _preLoadDom()
    {
        $original_document = $document;
        /****************************************
         * 前処理
         ****************************************/
        if ($base_dir)
        {
          $this->base_dir = $base_dir;
        }

        // loadHTML/saveHTMLのバグに対応。XML宣言の一時退避
        $declaration = '';
        if (preg_match('/^<\?xml\s[^>]+?\?>\s*/', $document, $e))
        {
          $declaration = $e[0];
          $document = substr($document, strlen($declaration));
        }

        // 同様に、<br />が<br>になってしまう問題のために退避
        #TODO: meta hr 等も同様だが、危険なのでさける。。。本質的な解決になっていない。
        $document = preg_replace('/<(br\s*.*\/)>/', 'HTMLCSSBRESCAPE%$1%::::::::', $document);

        // 文字参照をエスケープ
        $document = preg_replace('/&(#(?:\d+|x[0-9a-fA-F]+)|[A-Za-z0-9]+);/', 'HTMLCSSINLINERESCAPE%$1%::::::::', $document);

        // CDATAを退避
        $cdata_pattern = '/' . preg_quote('<![CDATA[') . '.*' . preg_quote(']]>') . '/Us';
        $escaped_cdata = null;
        if($num_matched = preg_match_all($cdata_pattern, $document, $e))
        {
          $escaped_cdata = $e[0];
          for($i = 0; $i < $num_matched; $i++)
          {
            $cdata_replacements[] = "HTMLCSSCDATAPLACEHOLDER$i::::::::";
            $cdata_patterns[] = $cdata_pattern;
          }
          $document = preg_replace($cdata_patterns, $cdata_replacements, $document, 1);
        }


        // 機種依存文字がエラーになる問題を回避するため、UTF-8に変換して処理
        $doc_encoding = mb_detect_encoding($document, 'sjis-win, UTF-8, eucjp-win');

        switch (strtolower($doc_encoding))
        {
          case 'sjis-win':
            $html_encoding = 'Shift_JIS';
            break;
          case 'eucjp-win':
            $html_encoding = 'EUC-JP';
            break;
          default:
            $html_encoding = '';
            break;
        }

        if ($doc_encoding != 'UTF-8')
        {
          $document = str_replace(array('UTF-8', $html_encoding), array('@####UTF8####@', 'UTF-8'), $document);
          $document = mb_convert_encoding($document, 'UTF-8', $doc_encoding);
        }

    }

    protected function _postApply($body)
    {
    }
}

class Userland_HTML_CSS_InlineInjector
    extends HTML_CSS_InlineInjector_Abstract
{
    public static function applyControllerResponse($mixed)
    {
        if ($mixed instanceof Zend_Controller_Response_Abstract)
        {
            $response = Diggin_Http_Response_Charset::wrapResponse($mixed);
            //....
            
            $self = new self;
            $self->apply($reponse->getBody());
        }
    }   

    public function _preLoadDom()
    {
        // won't use mb_detect_encoding without header!!!!
    }
}


exit;
$userland = new Userland_HTML_CSS_InlineInjector;
var_dump($userland);
