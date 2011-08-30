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

class DecoHighlighter_KeywordClassModifier extends DecoHighlighter_AbstractClassModifier implements DecoHighlighter_IClassModifier
{
	private $tokens;

	/**
	 *	constructor
	 *
	 */
	public function __construct()
	{
		$this->tokens = array( T_ABSTRACT, T_ARRAY, T_ARRAY_CAST, T_AS, T_BREAK, T_CASE, T_CATCH, T_CLASS, 
			T_CLONE, T_CONST, T_CONTINUE, T_DECLARE, T_DEFAULT, T_DO, T_ECHO, T_ELSE, T_ELSEIF, T_EMPTY,
			T_ENDDECLARE, T_ENDFOR, T_ENDFOREACH, T_ENDIF, T_ENDSWITCH, T_ENDWHILE, T_EVAL, T_EXIT, 
			T_EXTENDS, T_FOR, T_FOREACH, T_FUNCTION, T_GLOBAL, T_IF, T_INCLUDE, T_INCLUDE_ONCE, T_ISSET, 
			T_LIST, T_LOGICAL_AND, T_LOGICAL_OR, T_LOGICAL_XOR, T_NEW, T_PRINT, T_REQUIRE, T_REQUIRE_ONCE, 
			T_RETURN, T_STATIC, T_SWITCH, T_THROW, T_TRY, T_UNSET, T_VAR, T_WHILE
		);

		if ( version_compare(PHP_VERSION,"5.0.0") >= 0 ){
			$keyword_tokens_php_5_0_0 = array( T_FINAL, T_IMPLEMENTS, T_INSTANCEOF, T_INTERFACE, 
					T_PRIVATE, T_PUBLIC, T_PROTECTED, T_UNSET_CAST
				);
			$this->tokens = array_merge($this->tokens,$keyword_tokens_php_5_0_0);
		}

		if ( version_compare(PHP_VERSION,"5.1.0") >= 0 ){
			$keyword_tokens_php_5_1_0 = array( T_HALT_COMPILER, );
			$this->tokens = array_merge($this->tokens,$keyword_tokens_php_5_1_0);
		}

		if ( version_compare(PHP_VERSION,"5.3.0") >= 0 ){
			$keyword_tokens_php_5_3_0 = array( T_GOTO, T_NAMESPACE );
			$this->tokens = array_merge($this->tokens,$keyword_tokens_php_5_3_0);
		}
	}

	/**
	 *	implements modifyToken method for DecoHighlighter_IClassModifier interface.
	 *
	 *  @param int $token_id token code
	 */
	public function modifyToken( $token_id )
	{
		return in_array($token_id,$this->tokens) ? "deco_keyword" : null;
	}
}
