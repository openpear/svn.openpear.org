<?php
/**
 *
 *   DecoHighlighter
 *
 *
 *   @package    DecoHighlighter
 *   @version    $id$
 *   @copyright  2011 authors
 *   @author     Katsuki Shutou <stk2k@sazysoft.com>
 *   @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

require_once 'ParseResult.php';
require_once 'IClassModifier.php';
require_once 'AbstractClassModifier.php';
require_once 'DefaultClassModifier.php';
require_once 'PHPConstantClassModifier.php';
require_once 'LanguageConstructClassModifier.php';
require_once 'KeywordClassModifier.php';

class DecoHighlighter
{
	const TYPE_STRING    = 1;
	const TYPE_FILEPATH      = 2;
	const TYPE_URI           = 3;

	const DEFAULT_NO_CLASS_MODIFIERS       = false;

	private $class_modifiers;
	private $source;
	private $type;
	private $options;

	/**
	 *	constructor
	 *
	 *  @param string $source source code or path or URI to source code
	 *  @param int $type source parameter type below
	 *            TYPE_STRING  $source parameter means source code string data
	 *            TYPE_FILEPATH    $source parameter means local file system path
	 *            TYPE_URI         $source parameter means URI
	 *  @param array $options options below
	 *       $options = array(
	 *             "no_class_modifiers",      // no default class modifiers [bool]
	 *          )
	 */
	public function __construct( $source, $type, $options = null )
	{
		$this->source = $source;
		$this->type = $type;

		if ( !$options || !is_array($options) ){
			$options = array();
		}

		// set default options
		$default_options = array(
					"no_class_modifiers"      => self::DEFAULT_NO_CLASS_MODIFIERS,
				);

		foreach( $default_options as $key => $default_value ){
			if ( !isset($options[$key]) ){
				$options[$key] = $default_value;
			}
		}
		$this->options = $options;

		// default class modifiers
		if ( $options['no_class_modifiers'] == false ){
			$this->class_modifiers = array(
					new DecoHighlighter_DefaultClassModifier(),
					new DecoHighlighter_PHPConstantClassModifier(),
					new DecoHighlighter_LanguageConstructClassModifier(),
					new DecoHighlighter_KeywordClassModifier(),
				);
		}
	}

	/**
	 *	add class modifer 
	 *
	 *  @param array $modifier object that implements DecoHighlighter_IClassModifier
	 */
	public function addClassModifier( $modifier )
	{
		$modifier_class_name = get_class($modifier);

		foreach( $this->class_modifiers as $m ){
			if ( get_class($m) == $modifier_class_name ){
				// already added
				return;
			}
		}

		// new modifier
		$this->class_modifiers[] = $modifier;
	}

	/**
	 *	remove class modifer 
	 *
	 *  @param array $modifier object that implements DecoHighlighter_IClassModifier
	 */
	public function removeClassModifier( $modifier_class_name )
	{
		foreach( $this->class_modifiers as $key => $modifier ){
			if ( get_class($m) == $modifier_class_name ){
				unsset( $this->class_modifiers[$key] );
			}
		}
	}

	/**
	 *	parse source code
	 *
	 *  @param string $source source code
	 *  @param DecoHighlighter $highlighter DecoHighlighter instance
	 */
	public function parse()
	{
		return self::_parseStatic( $this );
	}

	/**
	 *	parse source code
	 *
	 *  @param string $source source code
	 *  @param DecoHighlighter $highlighter DecoHighlighter instance
	 */
	public static function parseFromString( $source, $highlighter = null )
	{
		if ( !$highlighter ){
			$highlighter = new DecoHighlighter( $source, self::TYPE_STRING );
		}
		return self::_parseStatic( $highlighter );
	}

	/**
	 *	parse source code
	 *
	 *  @param string $path source code path
	 *  @param DecoHighlighter $highlighter DecoHighlighter instance
	 */
	public static function parseFromFile( $path, $highlighter = null )
	{
		if ( !$highlighter ){
			$highlighter = new DecoHighlighter( $path, self::TYPE_FILEPATH );
		}
		return self::_parseStatic( $highlighter );
	}

	/**
	 *	parse source code
	 *
	 *  @param string $uri source code URI
	 *  @param DecoHighlighter $highlighter DecoHighlighter instance
	 */
	public static function parseFromURI( $uri, $highlighter = null )
	{
		if ( !$highlighter ){
			$highlighter = new DecoHighlighter( $uri, self::TYPE_URI );
		}
		return self::_parseStatic( $highlighter );
	}

	/**
	 *	parse source code
	 *
	 *  @param DecoHighlighter $highlighter DecoHighlighter instance
	 *
	 *  @return DecoHighlighter_ParseResult parse result HTML
	 */
	private static function _parseStatic( $highlighter )
	{
		$source = $highlighter->source;
		$type = $highlighter->type;

		// validate input
		$source_contents = "";
		$file_name = "";
		switch( $type )
		{
			case self::TYPE_STRING:
				if ( !is_string($source) ){
					throw new DecoHighlighter_ParseException("parameter 1 must be string.");
				}
				$source_contents = $source;
				break;
			case self::TYPE_FILEPATH:
				if ( !is_readable($source) ){
					throw new DecoHighlighter_ParseException("specified path is not file or unreadable.");
				}
				$file_name = $source;
				$source_contents = file_get_contents($source);
				break;
			case self::TYPE_URI:
				if ( !preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/',$uri) ){
					throw new DecoHighlighter_ParseException("specified URI seems to be wrong.");
				}
				$file_name = $source;
				$source_contents = file_get_contents($source);
				break;
		}

		// check failure
		if ( false === $source_contents ){
			throw new DecoHighlighter_ParseException("file input failed.");
		}

		// PHP function check
		if ( !function_exists("token_get_all") ){
			throw new DecoHighlighter_ParseException("token_get_all function not exists. you must enable Tokenizer PHP module.");
		}

		// decompose source code to tokens
		$tokens = token_get_all( $source_contents );

		return new DecoHighlighter_ParseResult( $file_name, $tokens, $highlighter->class_modifiers );
	}

}

class DecoHighlighter_ParseException extends Exception
{
}

class DecoHighlighter_RenderException extends Exception
{
}





