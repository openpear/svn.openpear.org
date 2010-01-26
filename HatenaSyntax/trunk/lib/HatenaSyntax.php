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
include_once dirname(__FILE__) . '/HatenaSyntax/CommentRemover.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Tree/INode.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Tree/Node.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Tree/Root.php';
include_once dirname(__FILE__) . '/HatenaSyntax/Tree/Leaf.php';

class HatenaSyntax
{
    /**
     * 文字列をパースしてHatenaSyntax_Nodeインスタンスからなる構文木を返す。
     *
     * @param string
     * @return HatenaSyntax_Node
     */
    static function parse($str)
    {
        return HatenaSyntax_Locator::it()->parser->parse(self::context($str));
    }
    
    /**
     * 文字列をパースしてhtmlを返す。
     *
     * @param string
     * @param Array
     * @return string
     */
    static function render($str, Array $config = array())
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
        $str = str_replace(array("\r\n", "\r"), "\n", $str);
        $str = strpos('<!--', $str) === false ? $str : HatenaSyntax_CommentRemover::remove($str);

        return PEG::context(preg_split("/\n/", $str));
    }

    /**
     * HatenaSyntax_Nodeインスタンスからなる構文木をhtmlにして返す。
     *
     * @param HatenaSyntax_Node 
     * @param Array
     * @return string
     */
    static function renderNode(HatenaSyntax_Node $root, Array $config = array())
    {
        $renderer = new HatenaSyntax_Renderer($config);
        return $renderer->render($root);
    }

    /**
     * セクション名を取得する。
     * 見つからなかった場合は空の文字列を返す。
     *
     * @param HatenaSyntax_Node
     * @return string
     */
    static function getSectionName(HatenaSyntax_Node $root)
    {
        if ($root->getType() !== 'root') {
            throw new InvalidArgumentException('$root must be root node');
        }

        foreach ($root->getData() as $block) {
            if ($block->getType() === 'header') {
                $data = $block->getData();
                if (is_string($data['name'])) {
                    return $data['name'];
                }
            }
        }

        return '';
    }
}
