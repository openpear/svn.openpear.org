<?php
/**
 * PHPTAL_Filter_Ktai.php
 *
 * @category PHPTAL
 * @package PHPTAL_Filter_Ktai
 * @author TANAKA Koichi <tanaka@ensites.net>
 * @copyright authors
 * @license MIT License
 * @version $Id$
 */

require_once 'PHPTAL/Filter.php';
/**
 * PHPTAL_Filter_Ktai
 *
 * PHPTALのフィルタとして使えるHTML_CSS_Mobileのラッパー
 * 将来的にはText_Pictogram_Mobileも使うようにするかもしれない。
 *
 * @author TANAKA Koichi <tanaka@ensites.net>
 */
class PHPTAL_Filter_Ktai implements PHPTAL_Filter
{
    /**
     * @var HTML_CSS_Mobile
     */
    protected $htmlCssMobile;

    /**
     * コンストラクタ
     *
     * @param         array $parameters
     * @author        TANAKA Koichi <tanaka@ensites.com>
     */
    public function  __construct(array $parameters = array()) {
        // デフォルト値
        $parameters += array(
            'css_base_dir'=>null,
            'css_mode'=>null,
            'css_files'=>null
        );

        $this->htmlCssMobile = HTML_CSS_Mobile::getInstance();
        if($parameters['css_base_dir']) {
            $this->htmlCssMobile->setBaseDir($parameters['css_base_dir']);
        }
        if($parameters['css_mode']) {
            $this->htmlCssMobile->setMode($parameters['css_mode']);
        }
        if($parameters['css_files']) {
            $this->htmlCssMobile->addCssFiles($parameters['css_files']);
        }
    }

   /**
    * HTML_CSS_Mobileのインスタンスを取得する
    * 
    * @return HTML_CSS_Mobile
    * @author       TANAKA Koichi <tanaka@ensites.com>
    * @since        0.0.1
    */
    public function getHtmlCssMobile()
    {
        return $this->htmlCssMobile;
    }

    public function filter($src)
    {
        return $this->htmlCssMobile->apply($src);
    }
}
?>
