<?php
/**
 *  Ethna_Renderer_RuneMaster.php (experimental)
 *
 *  @author     Takuya Sato < takuya0219 at gmail dot com >
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id: Ethna_Renderer_RuneMaster.php,v 1.00 2009/03/13 18:28:30 nazo Exp $
 */

/**
 *  RuneMasterレンダラクラス
 *
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Renderer_RuneMaster extends Ethna_Renderer
{
    /** @var    string compile directory  */
    var $template_dir = '';
    var $params = array();

    /**
     * plugin list
     * @var     array
     * @access  protected
     */
    var $smarty_plugin_list = array();

    /**
     * RuneMaster TemplateParser
     * @var     TemplateParser_Ethna $engine
     * @access  protected
     */
    var $engine;
    
    /**
     *  Ethna_Renderer_RuneMasterクラスのコンストラクタ
     *
     *  @access public
     */
    function Ethna_Renderer_RuneMaster(&$controller)
    {
        parent::Ethna_Renderer($controller);
        
        $this->template_dir = $controller->getTemplatedir();

        $this->engine =& new Ethna_Rune_Master($this->template_dir);
    }
    
    /**
     *  ビューを出力する
     *
     *  @access public
     *  @param  string  $template   テンプレート名
     *  @param  bool    $capture    true ならば出力を表示せずに返す
     */
    function perform($template = null, $capture = false)
    {
        if ($template === null && $this->template === null) {
            return Ethna::raiseWarning('template is not defined');
        }

        if ($template !== null) {
            $this->template = $template;
        }
        $this->engine->assign($this->params);

        $suffix = '.html';
        if (preg_match('/\.([a-zA-Z0-9_]+)$/', $this->template, $matches, PREG_OFFSET_CAPTURE)) {
            $suffix = $matches[0][0];
            $this->engine->setTemplateSuffix($suffix);
            $this->template = substr($this->template, 0, $matches[0][1]);
        }

        if  (is_readable($this->template_dir .DIRECTORY_SEPARATOR. $this->template. $suffix)) {
                if ($capture === true) {
                    $captured = $this->engine->cast($this->template);
                    return $captured;
                } else {
                    $captured = $this->engine->cast($this->template);
                    print($captured);
                }
        } else {
            return Ethna::raiseWarning('template not found ' . $this->template);
        }
    }

    /**
     * テンプレート変数を取得する
     * 
     *  @todo fixme
     *  @access public
     *  @param string $name  変数名
     *  @return mixed　変数
     */
    function &getProp($name = null)
    {
        $property =& $this->params[$name];

        if ($property !== null) {
            return $property;
        }

        return null;
    }

    /**
     *  テンプレート変数を削除する
     * 
     *  @param name    変数名
     *  @todo
     *  @access public
     */
    function removeProp()
    {
        $this->params = array();
 //       $this->engine->clearVariable(func_num_args());
    }

    /**
     *  テンプレート変数に配列を割り当てる
     * 
     *  @param array $array
     *  @access public
     */
    function setPropArray($array)
    {
        $this->params = $array;
        // $this->engine->assign($array);
    }

    /**
     *  テンプレート変数に配列を参照として割り当てる
     * 
     *  @param array $array
     *  @todo no implement
     *  @access public
     */
    function setPropArrayByRef(&$array)
    {
        $this->params = $array;
        //$this->engine->assign_by_ref($array);
    }

    /**
     *  テンプレート変数を割り当てる
     * 
     *  @param string $name 変数名
     *  @param mixed $value 値
     * 
     *  @access public
     */
    function setProp($name, $value)
    {
        $this->params[$name] = $value;
        //$this->engine->assign(array($name=>$value));
    }

    /**
     *  テンプレート変数に参照を割り当てる
     * 
     *  @access public
     *  @todo fixme
     *  @param string $name 変数名
     *  @param mixed $value 値
     */
    function setPropByRef($name, &$value)
    {
        $this->params[$name] = $value;
        //$this->engine->assign_by_ref($name, $value);
    }

    /**
     * setPlugin
     *
     * @access public
     */
    function setPlugin($name)
    {
        $this->engine->setSpells($name);
    }
}


class Ethna_Rune_Master extends Rune_Master
{
    public function __construct($templateDirectory)
    {
        parent::__construct($templateDirectory);
    }

    public function cast($templateName, $variables = null)
    {
        $this->_runic->setTemplate($templateName);
        $this->_runic->setTemplateSuffix($this->_templateSuffix);

        //$filename = "{$templateName}{$this->_templateSuffix}";
        $filename = "{$templateName}{$this->_templateSuffix}";
        $templateFile = "{$this->_templateDirectory}/{$filename}";

        $stone = new Rune_Stone();

        try {
            $stone->setTemplate($templateFile, $variables);
            $spells = $this->_runic->getSpells();
            foreach ($spells as $spell) {
                $spell->carve($stone);
            }
            return $this->scan($stone);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
}
