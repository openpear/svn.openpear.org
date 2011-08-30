<?php
/**
 *
 *   DecoHighlighter_IClassModifier
 *
 *
 *   @package    DecoHighlighter
 *   @version    $id$
 *   @copyright  2011 authors
 *   @author     Katsuki Shutou <stk2k@sazysoft.com>
 *   @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

interface DecoHighlighter_IClassModifier
{
	/**
	 *	This method will be called when parser find token in your source code. 
	 *  You can add CSS class by returning CSS class name in this method.
	 *  The CSS class name you return will be embedded as class attribute in span tag.
	 *  If you returned null, no additional class is applied.
	 *
	 *  @param int $token_id token code
	 *
	 *	@see http://www.php.net/manual/tokens.php
	 */
	public function modifyToken( $token_id );

	/**
	 *	This method will be called when parser find sign in your source code. 
	 *  You can add CSS class by returning CSS class name in this method.
	 *  The CSS class name you return will be embedded as class attribute in span tag.
	 *  If you returned null, no additional class is applied.
	 *
	 *  @param string $sign sign like ( ) " ' { } = : ; [ ]
	 *
	 *	@see http://www.php.net/manual/tokens.php
	 */
	public function modifySign( $sign );
}
