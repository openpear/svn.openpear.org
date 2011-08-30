<?php
/**
 *
 *   DecoHighlighter_ParseResult
 *
 *
 *   @package    DecoHighlighter
 *   @version    $id$
 *   @copyright  2011 authors
 *   @author     Katsuki Shutou <stk2k@sazysoft.com>
 *   @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

class DecoHighlighter_ParseResult extends Exception
{
	const DEFAULT_EMBED_CSS       = false;
	const DEFAULT_CSS_PATH        = "../css/deco_highlighter.css";
	const DEFAULT_CSS_CHARSET     = "utf-8";
	const DEFAULT_HTML_HEADER     = true;
	const DEFAULT_HTML_TITLE      = "Deco Highlighter";
	const DEFAULT_HEADER_FORMAT   = '<table class="deco_table">';
	const DEFAULT_LINE_FORMAT     = '<tr class="deco_row"><td class="deco_linenum" nowrap>{%line%}</td><td class="deco_source" nowrap>{%source%}</td></tr>';
	const DEFAULT_FOOTER_FORMAT   = '</table>';
	const DEFAULT_TABSTOPS        = 2;

	private $tokens;
	private $file_name;
	private $class_modifiers;
	private $styles;

	/**
	 *	constructor
	 *
	 *  @param string $file_name file name of source code
	 *  @param array $tokens tokens of source code
	 *  @param array $class_modifiers object array that implements DecoHighlighter_IClassModifier
	 */
	public function __construct( $file_name, $tokens, $class_modifiers )
	{
		$this->file_name       = $file_name;
		$this->tokens          = $tokens;
		$this->class_modifiers = $class_modifiers;
		$this->styles          = null;
	}

	/**
	 *	add style
	 *
	 *  @param string $format line format
	 *  @param string $line_src part of source code without EOL
	 *  @param string $line_no line number
	 *  @return string formatted HTML
	 */
	public function addStyle( $style )
	{
		$this->styles[] = $style;
	}

	/**
	 *	render HTML
	 *
	 *  @param array $options options below
	 *       $options = array(
	 *             "embed_css",      // embedding css enabled [bool]
	 *             "css_path",       // css file path [string]
	 *             "css_charset",    // css file encoding [string]
	 *             "html_header",    // if you want to output HTML header, this option should be true [bool]
	 *             "html_title",     // when 'html_header' option is enabled, this options set HTML title [string]
	 *             "header_format",  // header formatter [string]
	 *             "line_format",    // line formatter [string]
	 *             "footer_format",  // footer formatter [string]
	 *             "tab_stops",      // tab stops [int]
	 *          )
	 *  @param bool $output If true, output HTML
	 */
	public function render( $options = null, $output = true )
	{
		if ( !$options || !is_array($options) ){
			$options = array();
		}

		// set default options
		$default_options = array(
					"embed_css"      => self::DEFAULT_EMBED_CSS,
					"css_path"       => self::DEFAULT_CSS_PATH,
					"css_charset"    => self::DEFAULT_CSS_CHARSET,
					"html_header"    => self::DEFAULT_HTML_HEADER,
					"html_title"     => self::DEFAULT_HTML_TITLE,
					"header_format"  => self::DEFAULT_HEADER_FORMAT,
					"line_format"    => self::DEFAULT_LINE_FORMAT,
					"footer_format"  => self::DEFAULT_FOOTER_FORMAT,
					"tab_stops"      => self::DEFAULT_TABSTOPS,
				);

		foreach( $default_options as $key => $default_value ){
			if ( !isset($options[$key]) ){
				$options[$key] = $default_value;
			}
		}

		// start output HTML
		$html = "";

		// output HTML header
		if ( $options['html_header'] === true )
		{
			$html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . PHP_EOL;
			$html .= '<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">' . PHP_EOL;
			$html .= '<head>' . PHP_EOL;
			$html .= '<meta http-equiv="content-Type" content="text/html; charset=utf-8">' . PHP_EOL;
			$html .= '<title>' . $options['html_title'] . ':' . $this->file_name . '</title>' . PHP_EOL;
			if ( $options['embed_css'] === false ){
				$css_path = $options['css_path'];
				$css_charset = $options['css_charset'];
				$html .= '<link rel="stylesheet" href="' . $css_path . '" type="text/css" charset="' . $css_charset . '" />' . PHP_EOL;
			}
			$html .= '<body>' . PHP_EOL;
		}

		// output embedded css
		if ( $options['embed_css'] === true )
		{
			$css_path = $options['css_path'];
			$css_contents = file_get_contents($css_path);
			if ( $css_contents === false ){
				throw new DecoHighlighter_RenderException("can't read css file:{$css_path}");
			}
			$html .= '<style type="text/css"><!--' . PHP_EOL;
			$html .= strip_tags($css_contents);
			$html .= '--></style>' . PHP_EOL;
		}

		// additonal styles
		if ( $this->styles && is_array($this->styles) )
		{
			$html .= '<style type="text/css"><!--' . PHP_EOL;
			foreach( $this->styles as $style ){
				$html .= $style;
			}
			$html .= '--></style>' . PHP_EOL;
		}

		// output body html
		$html .= $options['header_format'] . PHP_EOL;

		$line_no = 1;
		$line_format = $options['line_format'];
		while( ($line=$this->nextLine($options)) !== false ){
			$line_html = $this->formatLine($line_format,$line,$line_no);
			$html .= $line_html . PHP_EOL;
			$line_no ++;
		}

		$html .= $options['footer_format'] . PHP_EOL;

		// output HTML footer
		if ( $options['html_header'] === true )
		{
			$html .= '</html>' . PHP_EOL;
		}

		if ( $output ){
			echo $html;
		}

		return $html;
	}

	/**
	 *	get source code line string and move internal poisition to next line top.
	 *
	 *  @param array $options options 
	 *  @return string source code of current line, or false when reach EOF
	 */
	private function nextLine($options)
	{
		$sign_tokens = array( "(", ")", "\"", "'", "{", "}", "=", ":", ";", "[", "]" );

		$token = array_shift($this->tokens);

		if ( $token === null ){
			return false;
		}

		$current_line = "";
		while( $token )
		{
			$new_line = $this->checkNewLine($token);

			if ( $token[0] == T_WHITESPACE ){
				$ch_array = str_split($token[1]);
				foreach( $ch_array as $ch )
				{
					if ( $ch === "\t" ){
						$current_line .= str_repeat("&nbsp;",$options['tab_stops']);
					}
					else if ( $ch === " " ){
						$current_line .= "&nbsp;";
					}
				}
			}
			else if ( in_array($token[0],$sign_tokens) ){
				$css_class = "";
				// call class modifiers
				if ( $this->class_modifiers && is_array($this->class_modifiers) )
				{
					foreach( $this->class_modifiers as $modifier )
					{
						if ( $modifier instanceof DecoHighlighter_IClassModifier ){
							$ret = $modifier->modifySign($token[0]);
							if ( is_string($ret) ){
								$css_class .= " {$ret}";
							}
						}
					}
				}
				$current_line .= '<span class="' . $css_class . '">' . $token[0] . '</span>';
			}
			else{
				if ( is_string($token[0]) ){
					$current_line .= $token[0];
				}
				else if ( is_numeric($token[0]) ){
					$css_class = "";
					// call class modifiers
					if ( $this->class_modifiers && is_array($this->class_modifiers) )
					{
						foreach( $this->class_modifiers as $modifier )
						{
							if ( $modifier instanceof DecoHighlighter_IClassModifier ){
								$ret = $modifier->modifyToken($token[0]);
								if ( is_string($ret) ){
									$css_class .= " {$ret}";
								}
							}
						}
					}
					$current_line .= '<span class="' . $css_class . '">' . $this->escape($token[1]) . '</span>';
				}
			}

			if ( $new_line ){
				return $current_line;
			}

			$token = array_shift($this->tokens);
		}

		return $current_line;
	}

	/**
	 *	format line
	 *
	 *  @param string $format line format
	 *  @param string $line_src part of source code without EOL
	 *  @param string $line_no line number
	 *  @return string formatted HTML
	 */
	private function formatLine( $format, $line_src, $line_no )
	{
		$out = str_replace('{%source%}', $line_src, $format);
		$out = str_replace('{%line%}', $line_no, $out);

		return $out;
	}

	/**
	 *	escape string
	 *
	 *  @param string $input input string
	 *  @return string escaped string
	 */
	private function escape( $input )
	{
		$out = htmlspecialchars($input);
		$out = str_replace(" ","&nbsp;",$out);
		$out = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$out);
		$out = nl2br($out);

		return $out;
	}

	/**
	 *	push back token
	 *
	 *  @param array $token_id token_id
	 *  @param array $token_data token_data
	 *  @param string $crlf new line character("\r\n"/"\r"/"\n")
	 */
	private function unshiftToken( $token_id, $token_data, $crlf = null )
	{
		if ( mb_strlen($token_data) === 0 && $crlf ){
			if ( count($this->tokens) > 0 ){
				$top = array_shift( $this->tokens );
				if ( $top[0] === T_WHITESPACE ){
					$top[1] = $crlf . $top[1];
					array_unshift( $this->tokens, $top );
				}
				else{
					array_unshift( $this->tokens, $top );
					array_unshift( $this->tokens, array(T_WHITESPACE,$crlf) );
				}
			}
			else{
				array_unshift( $this->tokens, array(T_WHITESPACE,$crlf) );
			}
		}
		else if ( !preg_match('/[^\x0a\x0d]+/', $token_data) && count($this->tokens) > 0 ){
			// if the top of token stack is T_WHITESPACE and all of token_data is consists of CR or LF, merge them.
			$top = array_shift( $this->tokens );
			if ( $top[0] === T_WHITESPACE ){
				$top[1] = $token_data . $top[1];
				array_unshift( $this->tokens, $top );
			}
			else{
				array_unshift( $this->tokens, $top );
				array_unshift( $this->tokens, array(T_WHITESPACE,$token_data) );
			}
		}
		else{
			array_unshift( $this->tokens, array($token_id,$token_data) );
		}
	}

	/**
	 *	check new line
	 *
	 *  @param array $token token
	 *  @return array/string If array, below 3 elements. If string, no new line code found.
	 */
	private function checkNewLine( &$token )
	{
		$new_line = false;

		if ( mb_strpos($token[1],"\r\n") === 0  ){
			$next_lines = mb_substr($token[1],2);
			if ( mb_strlen($next_lines) > 0 ){
				$this->unshiftToken( $token[0], $next_lines, "\r\n" );
			}
			$new_line = true;
			$token[1] = mb_substr($token[1],0,2);
			$token[0] === T_WHITESPACE;
		}
		else if ( mb_strpos($token[1],"\r") === 0 ){
			$next_lines = mb_substr($token[1],1);
			if ( mb_strlen($next_lines) > 0 ){
				$this->unshiftToken( $token[0], $next_lines, "\r" );
			}
			$new_line = true;
			$token[1] = mb_substr($token[1],0,1);
			$token[0] === T_WHITESPACE;
		}
		else if ( mb_strpos($token[1],"\n") === 0 ){
			$next_lines = mb_substr($token[1],1);
			if ( mb_strlen($next_lines) > 0 ){
				$this->unshiftToken( $token[0], $next_lines, "\n" );
			}
			$new_line = true;
			$token[1] = mb_substr($token[1],0,1);
			$token[0] === T_WHITESPACE;
		}
		else{
			$length = mb_strlen($token[1]);

			$pos1 = ( $length >= 2 ) ? mb_strpos($token[1],"\r\n") : false;
			$pos2 = ( $length >= 1 ) ? mb_strpos($token[1],"\r") : false;
			$pos3 = ( $length >= 1 ) ? mb_strpos($token[1],"\n") : false;

			$min_pos = $length + 1;
			if ( $pos1 && $pos1 >= 0 ){
				$min_pos = min($min_pos,$pos1);
			}
			if ( $pos2 && $pos2 >= 0 ){
				$min_pos = min($min_pos,$pos2);
			}
			if ( $pos3 && $pos3 >= 0 ){
				$min_pos = min($min_pos,$pos3);
			}

			if ( $pos1 >= 0 && $min_pos == $pos1 ){
				$next_lines = mb_substr($token[1],$pos1+2);
				$this->unshiftToken( $token[0], $next_lines );
				$new_line = true;
				$token[1] = mb_substr($token[1],0,$pos1);
			}
			else if ( $pos2 >= 0 && $min_pos == $pos2 ){
				$next_lines = mb_substr($token[1],$pos2+1);
				$this->unshiftToken( $token[0], $next_lines, "\r" );
				if ( mb_strlen($next_lines) > 0 && mb_substr($next_lines,0,1) !== "\n" ){
					$new_line = true;
				}
				$token[1] = mb_substr($token[1],0,$pos2);
			}
			else if ( $pos3 >= 0 && $min_pos == $pos3 ){
				$next_lines = mb_substr($token[1],$pos3+1);
				$this->unshiftToken( $token[0], $next_lines );
				$new_line = true;
				$token[1] = mb_substr($token[1],0,$pos3);
			}
		}

		return $new_line;
	}
}
