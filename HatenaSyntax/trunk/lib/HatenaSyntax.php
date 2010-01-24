<?php
/**
 * @package HatenaSyntax
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

include_once 'PEG.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Node.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Regex.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Locator.php';
include_once dirname(__FILE__) . '/HatenaSyntax/NodeCreater.php';
include_once dirname(__FILE__) . '/HatenaSyntax/InlineTag.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Renderer.php';
include_once dirname(__FILE__) . '/HatenaSyntax/TOCRenderer.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Util.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Header.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Block.php';
include_once dirname(__FILE__) . '/HatenaSyntax/LineElement.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Quote.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Table.php';
include_once dirname(__FILE__) . '/HatenaSyntax/DefinitionList.php';
include_once dirname(__FILE__) . '/HatenaSyntax/List.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Paragraph.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Pre.php';
include_once dirname(__FILE__) . '/HatenaSyntax/SuperPre.php';
include_once dirname(__FILE__) . '/HatenaSyntax/TreeRenderer.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Tree.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Tree/INode.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Tree/Node.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Tree/Root.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Tree/Leaf.php';

class HatenaSyntax
{
    /**
     * @param string
     * @return HatenaSyntax_Node
     */
    static function parse($str)
    {
        return HatenaSyntax_Locator::it()->parser->parse(self::context($str));
    }
    
    /**
     * @param string
     * @param Array
     * @return string
     */
    static function render($str, $config = array())
    {
        $node = self::parse($str);
        $renderer = new HatenaSyntax_Renderer($config);
        return $renderer->render($node);
    }
    
    /**
     * @param string
     * @return PEG_IContext
     */
    static protected function context($str)
    {
        $str = preg_replace('/<!--.*?-->/s', '', $str);
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("\r", "\n", $str);

        return PEG::context(preg_split("/\n/", $str));
    }
}
