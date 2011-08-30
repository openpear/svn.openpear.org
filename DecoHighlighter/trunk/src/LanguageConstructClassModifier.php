<?php
/**
 *
 *   DecoHighlighter_LanguageConstructClassModifier
 *
 *
 *   @package    DecoHighlighter
 *   @version    $id$
 *   @copyright  2011 authors
 *   @author     Katsuki Shutou <stk2k@sazysoft.com>
 *   @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

class DecoHighlighter_LanguageConstructClassModifier extends DecoHighlighter_AbstractClassModifier implements DecoHighlighter_IClassModifier
{
	private $tokens;

	/**
	 *	constructor
	 *
	 */
	public function __construct()
	{
		$this->tokens = array( T_ECHO, T_PRINT, T_EXIT );
	}

	/**
	 *	implements modifyToken method for DecoHighlighter_IClassModifier interface.
	 *
	 *  @param int $token_id token code
	 */
	public function modifyToken( $token_id )
	{
		return in_array($token_id,$this->tokens) ? "deco_language_construct" : null;
	}
}
