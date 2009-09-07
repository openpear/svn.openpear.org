<?php
/**
 * @package HatenaSyntax
 * @author anatoo<anatoo@nequal.jp>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @version $Id$
 */

include_once 'PEG.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Node.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Locator.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Factory.php';
include_once dirname(__FILE__) . '/HatenaSyntax/NodeCreater.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Renderer.php';
include_once dirname(__FILE__) . '/HatenaSyntax/TOCRenderer.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Util.php';
include_once dirname(__FILE__) . '/HatenaSyntax/TreeRenderer.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Tree.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Tree/INode.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Tree/Node.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Tree/Root.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Tree/Leaf.php';

class HatenaSyntax
{
    static function parse($str)
    {
        return HatenaSyntax_Locator::it()->parser->parse(PEG::context($str));
    }
    
    static function render($str, $config = array())
    {
        $node = self::parse($str);
        $renderer = new HatenaSyntax_Renderer($config);
        return $renderer->render($node);
    }
}