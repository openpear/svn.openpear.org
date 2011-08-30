<?php
/**
 *
 *   DecoHighlighter_KeywordClassModifier
 *
 *
 *   @package    DecoHighlighter
 *   @version    $id$
 *   @copyright  2011 authors
 *   @author     Katsuki Shutou <stk2k@sazysoft.com>
 *   @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

abstract class DecoHighlighter_AbstractClassModifier implements DecoHighlighter_IClassModifier
{
	/**
	 *	implements modifyToken method for DecoHighlighter_IClassModifier interface.
	 *
	 *  @param int $token_id token code
	 */
	public function modifyToken( $token_id )
	{
		return null;
	}

	/**
	 *	implements modifySign method for DecoHighlighter_IClassModifier interface.
	 *
	 *  @param string $sign sign like ( ) " ' { } = : ; [ ]
	 */
	public function modifySign( $sign )
	{
		return null;
	}
}
