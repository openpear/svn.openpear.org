<?php
/**
 *
 *   DecoHighlighter_PHPConstantClassModifier
 *
 *
 *   @package    DecoHighlighter
 *   @version    $id$
 *   @copyright  2011 authors
 *   @author     Katsuki Shutou <stk2k@sazysoft.com>
 *   @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

class DecoHighlighter_PHPConstantClassModifier extends DecoHighlighter_AbstractClassModifier implements DecoHighlighter_IClassModifier
{
	private $tokens;

	/**
	 *	constructor
	 *
	 */
	public function __construct()
	{
		$this->tokens = array( T_CLASS_C, T_DIR, T_FILE, T_FUNC_C, T_LINE, T_METHOD_C, );

		if ( version_compare(PHP_VERSION,"5.3.0") >= 0 ){
			$const_tokens_php_5_3_0 = array( T_NS_C, T_USE );
			$this->tokens = array_merge($this->tokens,$const_tokens_php_5_3_0);
		}
	}

	/**
	 *	implements modifyToken method for DecoHighlighter_IClassModifier interface.
	 *
	 *  @param int $token_id token code
	 */
	public function modifyToken( $token_id )
	{
		return in_array($token_id,$this->tokens) ? "deco_const" : null;
	}
}
