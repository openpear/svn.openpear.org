<?php
/**
 *
 *   DecoHighlighter_DefaultClassModifier
 *
 *
 *   @package    DecoHighlighter
 *   @version    $id$
 *   @copyright  2011 authors
 *   @author     Katsuki Shutou <stk2k@sazysoft.com>
 *   @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

class DecoHighlighter_DefaultClassModifier implements DecoHighlighter_IClassModifier
{
	/**
	 *	constructor
	 *
	 */
	public function __construct()
	{
	}

	/**
	 *	implements modifyToken method for DecoHighlighter_IClassModifier interface.
	 *
	 *  @param int $token_id token code
	 */
	public function modifyToken( $token_id )
	{
		return 'deco_' . strtolower(token_name($token_id));
	}

	/**
	 *	implements modifySign method for DecoHighlighter_IClassModifier interface.
	 *
	 *  @param string $sign sign like ( ) " ' { } = : ; [ ]
	 */
	public function modifySign( $sign )
	{
		return 'deco_sign';
	}
}
