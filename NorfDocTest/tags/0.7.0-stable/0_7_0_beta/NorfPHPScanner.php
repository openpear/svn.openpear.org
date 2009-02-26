<?php

require_once 'NorfStringScanner.php';
require_once 'NorfPHPToken.php';
require_once 'NorfDocTestScanner.php';

class NorfPHPScanner extends NorfStringScanner
{

    const DEC_INT_PATTERN     = '/\G[\+\-]?(0|[1-9][0-9]*)/';
    const HEX_INT_PATTERN     = '/\G[\+\-]?0[xX][0-9a-fA-F]+/';
    const OCT_INT_PATTERN     = '/\G[\+\-]?0[0-7]+/';
    const FLOAT_PATTERN       = '/\G((([0-9]+)?\.([0-9]+)?)|[0-9]+)([eE][+-]?[0-9]+)?/';
    const BASE_IDENT_PATTERN  = '([a-zA-Z_][0-9a-zA-Z_]*)';
    const IDENT_PATTERN       = '/\G[a-zA-Z_][0-9a-zA-Z_]*/';
    const VAR_PATTERN         = '/\G\$[a-zA-Z_][0-9a-zA-Z_]*/';

    const TOKEN_PATTERNS = '/\G(\(|\)|\[|\]|;|,|::|:|@|\?|\$|`|\->|=>|=+|\!=*|[><\*\/%&\|\^\+\-]+=?|function|abstract)/';
    static $tagTable =
        array('(' => NorfPHPToken::LPAREN_TAG,
              ')' => NorfPHPToken::RPAREN_TAG,
              '[' => NorfPHPToken::LBRACK_TAG,
              ']' => NorfPHPToken::RBRACK_TAG,
              '{' => NorfPHPToken::LBRACE_TAG,
              '}' => NorfPHPToken::RBRACE_TAG,
              '==' => NorfPHPToken::EQ_TAG,
              '!=' => NorfPHPToken::NE_TAG,
              '===' => NorfPHPToken::IEQ_TAG,
              '!==' => NorfPHPToken::INE_TAG,
              '>' => NorfPHPToken::GT_TAG,
              '<' => NorfPHPToken::LT_TAG,
              '>=' => NorfPHPToken::GE_TAG,
              '<=' => NorfPHPToken::LE_TAG,
              '++' => NorfPHPToken::INC_TAG,
              '--' => NorfPHPToken::DEC_TAG,
              '+' => NorfPHPToken::ADD_TAG,
              '-' => NorfPHPToken::SUB_TAG,
              '*' => NorfPHPToken::MUL_TAG,
              '/' => NorfPHPToken::DIV_TAG,
              '%' => NorfPHPToken::MOD_TAG,
              '~' => NorfPHPToken::NOT_TAG,
              '&' => NorfPHPToken::AND_TAG,
              '|' => NorfPHPToken::OR_TAG,
              '^' => NorfPHPToken::XOR_TAG,
              '>>' => NorfPHPToken::LSHIFT_TAG,
              '<<' => NorfPHPToken::RSHIFT_TAG,
              '!' => NorfPHPToken::LNOT_TAG,
              '&&' => NorfPHPToken::LAND1_TAG,
              'and' => NorfPHPToken::LAND2_TAG,
              '||' => NorfPHPToken::LOR1_TAG,
              'or' => NorfPHPToken::LOR2_TAG,
              'xor' => NorfPHPToken::LXOR_TAG,
              '.' => NorfPHPToken::CONCATE_TAG,
              ',' => NorfPHPToken::COMMA_TAG,
              '`' => NorfPHPToken::BQUOTE_TAG,
              ':' => NorfPHPToken::COLON_TAG,
              '::' => NorfPHPToken::SEP_TAG,
              ';' => NorfPHPToken::TERM_TAG,
              '@' => NorfPHPToken::AT_TAG,
              '?' => NorfPHPToken::Q_TAG,
              '$' => NorfPHPToken::DOLLAR_TAG,
              '->' => NorfPHPToken::REF_TAG,
              '=>' => NorfPHPToken::ASSOC_TAG,

              '=' => NorfPHPToken::ASSIGN_TAG,
              '+=' => NorfPHPToken::ADD_ASSIGN_TAG,
              '-=' => NorfPHPToken::SUB_ASSIGN_TAG,
              '*=' => NorfPHPToken::MUL_ASSIGN_TAG,
              '/=' => NorfPHPToken::DIV_ASSIGN_TAG,
              '%=' => NorfPHPToken::MOD_ASSIGN_TAG,
              '~=' => NorfPHPToken::NOT_ASSIGN_TAG,
              '&=' => NorfPHPToken::AND_ASSIGN_TAG,
              '|=' => NorfPHPToken::OR_ASSIGN_TAG,
              '^=' => NorfPHPToken::XOR_ASSIGN_TAG,
              '>>=' => NorfPHPToken::LSHIFT_ASSIGN_TAG,
              '<<=' => NorfPHPToken::RSHIFT_ASSIGN_TAG,
              '.=' => NorfPHPToken::CONCATE_ASSIGN_TAG,

              'abstract' => NorfPHPToken::ABSTRACT_TAG,
              'const' => NorfPHPToken::CONST_TAG,
              'else' => NorfPHPToken::ELSE_TAG,
              'elseif' => NorfPHPToken::ELSE_TAG,
              'extends' => NorfPHPToken::EXTENDS_TAG,
              'final' => NorfPHPToken::FINAL_TAG,
              'function' => NorfPHPToken::FUNCTION_TAG,
              'if' => NorfPHPToken::IF_TAG,
              'implements' => NorfPHPToken::INTERFACE_TAG,
              'interface' => NorfPHPToken::IMPLEMENTS_TAG,
              'static' => NorfPHPToken::STATIC_TAG,
              'parent' => NorfPHPToken::PARENT_TAG,
              'private' => NorfPHPToken::PRIVATE_TAG,
              'protected' => NorfPHPToken::PROTECTED_TAG,
              'public' => NorfPHPToken::PUBLIC_TAG,
              'return' => NorfPHPToken::RETURN_TAG,
              'self' => NorfPHPToken::SELF_TAG,
              );

    protected $_tempLines = 0;
    protected $_tempColumns = 0;

    static function sourceCodeByRemovingComments($s, $path)
    {
        $code = '';
        $sc = New self($s, $path);
        $sc->_phpMode = true;
        $sc->_docTestMode = true;
        while ($tok = $sc->nextToken()) {
            if ($tok->tag() != NorfPHPToken::DOCTEST_TAG)
                $code .= ' ' . $tok->value();
        }
        return $code;
    }

    function __construct($src, $path='<unknown>')
    {
        parent::__construct($src, 0, NorfStringScanner::SPACES_SKIP);
        $this->_path = $path;
        $this->_phpMode = FALSE;
        $this->_docTestMode = false;
        $this->_nextTok = null;
        $this->_term = FALSE;
        $this->_className = null;
        $this->_braces = 0;
        $this->_inClass = true;
    }

    function nextToken()
    {
        if ($this->_term)
            return null;
        else if ($this->_nextTok) {
            $tok = $this->_nextTok;
            $this->_nextTok = null;
            return $tok;
        } else if (!$this->_phpMode) {
            if (($s = $this->scanUpToString('<?php')) !== null) {
                $this->_phpMode = TRUE;
                if ($s) {
                    $this->_nextTok = $this->createToken(NorfPHPToken::BEGIN_PHP_TAG, '<?php');
                    return $this->createToken(NorfPHPToken::TEXT_TAG, $s);
                } else
                    return $this->createToken(NorfPHPToken::BEGIN_PHP_TAG, '<?php');
            } else {
                $s = $this->restString();
                $this->_term = TRUE;
                if ($s)
                    return $this->createToken(NorfPHPToken::TEXT_TAG, $s);
                else
                    return null;
            }
        } else {
            /* line comment */
            while (!$this->isNextLocationAtEndOfString()) {
                if ($this->scanString('//'))
                    $this->scanUpToNewLine();
                else if ($this->scanPattern('/\G\/\*[^\*]/'))
                    $this->scanUpToString('*/');
                else
                    break;
            }
            if ($this->isNextLocationAtEndOfString()) {
                $this->_term = TRUE;
                return null;
            }

            $this->_tempLines = $this->lineNumber();
            $this->_tempColumns = $this->characterColumnNumber();
            if ($this->scanString("/**")) {
                if ($tok = $this->_scanDocumentationComment())
                    return $tok;
                else
                    return $this->nextToken();
            } else if ($this->scanString('{')) {
                $this->_braces++;
                return $this->createToken(NorfPHPToken::LBRACE_TAG, '{');
            } else if ($this->scanString('}')) {
                $this->_braces--;
                if ($this->_braces == 0) {
                    $this->_className = null;
                    $this->_inClass = false;
                }
                return $this->createToken(NorfPHPToken::RBRACE_TAG, '}');
            } else if (($name = $this->scanVariable()) !== null)
                return $this->createToken(NorfPHPToken::VAR_TAG, $name);
            else if ($this->scanString('<<<')) {
               $name = trim($this->scanUpToNewLine());
               $skip = $this->charactersToBeSkipped();
               $this->setCharactersToBeSkipped(null);
               $doc = '';
               while (!$this->scanString($name))
                   $doc = $this->scanUpToNewLine();
               $this->setCharactersToBeSkipped($skip);
               return $this->createToken(NorfPHPToken::STRING_TAG, $doc);
            } else if (($s = $this->scanPattern(self::TOKEN_PATTERNS)) !== null)
                return $this->createToken(self::$tagTable[$s], $s);
            else if (($val = $this->scanLiteralString()) !== null)
                return $this->createToken(NorfPHPToken::STRING_TAG, $val);
            else if (($val = $this->scanFloat()) !== null)
                return $this->createToken(NorfPHPToken::FLOAT_TAG, $val);
            else if (($val = $this->scanInteger()) !== null)
                return $this->createToken(NorfPHPToken::INT_TAG, $val);
            else if ($this->scanString('class')) {
                $this->_inClass = true;
                $this->_className =
                    $this->scanPatternNoAdvance(self::IDENT_PATTERN);
                return $this->createToken(NorfPHPToken::CLASS_TAG, 'class');
            } else if ($this->scanString('?>')) {
                $this->_phpMode = FALSE;
                return $this->createToken(NorfPHPToken::END_PHP_TAG, '?>');
            } else if (($id = $this->scanIdentifier()) !== null)
                return $this->createToken(NorfPHPToken::IDENT_TAG, $id);
            else if ($this->_docTestMode &&
                     ($dir = $this->scanPattern('/\G#[a-zA-Z0-9]+/')) !== null)
                return $this->createToken(NorfPHPToken::DOCTEST_DIR_TAG, $dir);
            else {
                $msg = 'unknown character -- `' .
                    $this->forwardString(5) . '...\'';
                throw new NorfPHPScannerError($msg, $this->_path,
                                              $this->lineNumber(),
                                              $this->characterColumnNumber());
            }
        }
    }

    const DOCTEST_MODULE = '/\G((public|protected|private|static|final|abstract)\s*)*(function|class)\s*[a-zA-Z_][0-9a-zA-Z_]*/m';

    function _scanDocumentationComment() {
        $lc = $this->location();
        $s = '';
        $skip = $this->charactersToBeSkipped();
        $this->setCharactersToBeSkipped(null);
        $this->scanWhitespaces();

        while (!$this->isAtEndOfString()) {
            if ($this->scanString("*/")) {
                $this->setCharactersToBeSkipped($skip);
                if ($func = $this->scanPatternNoAdvance(self::DOCTEST_MODULE)) {
                    $words = split(' ', $func);
                    $name = $words[count($words)-1];
                    $sc = new NorfDocTestScanner($this->_className, $name,
                                                 $this->string(),
                                                 $lc, $this->_path);
                    return $this->createToken(NorfPHPToken::DOCTEST_TAG, $sc);
                } else
                    return null;
            } else if ($nl = $this->scanNewlines()) {
                $s .= $nl;
                $this->scanWhitespaces();
                while (!$this->isAtEndOfString() &&
                       !$this->scanStringNoAdvance("*/"))
                    if (!$this->scanPattern("/\G\*+\s*/"))
                        break;
            } else
                $s .= $this->scanCharacter();
        }
        throw new NorfPHPScannerError('reach end of source');
    }

    function createToken($tag, $val)
    {
        return new NorfPHPToken($this->_path,
                               $this->_tempLines,
                               $this->_tempColumns,
                               $tag, $val);
    }

    function ensureNextToken()
    {
        $tags = func_get_args();
        $tok = $this->nextToken();
        if ($tok === null)
            throw NorfDocTestParseError('reach end of source');
        else if (!in_array($tok->tag(), $tags))
            throw NorfDocTestParseError::
                withToken($tok, 'expected ' . implode(' or ', $tags) .
                          ' -- ' . $tok->tag());
        else if ($tags) {
            $toks = array();
            foreach ($tags as $tag) {
                if ($tok->isTag($tag))
                    $toks[] = $tok;
                else
                    $toks[] = null;
            }
            return $toks;
        } else
            return $tok;
    }

    function ensureNextTokenValue()
    {
        $tags = func_get_args();
        $toks = call_user_method_array('ensureNextToken', $this, $tags);
        if ($tags) {
            foreach ($toks as $tok)
                if ($tok)
                    return $tok->value();
        } else
            return $toks;
    }

    function scanInteger()
    {
        $match = $this->scanHexInteger();
        if ($match === null) {
            $match = $this->scanOctalInteger();
            if ($match === null)
                $match = $this->scanDecimalInteger();
        }
        return $match;
    }

    function scanDecimalInteger()
    {
        $match = $this->scanPattern(self::DEC_INT_PATTERN);
        if ($match === null)
            return null;
        else
            return intval($match);
    }

    function scanHexInteger()
    {
        $match = $this->scanPattern(self::HEX_INT_PATTERN);
        if ($match === null)
            return null;
        else
            return hexdec($match);
    }

    function scanOctalInteger()
    {
        $match = $this->scanPattern(self::OCT_INT_PATTERN);
        if ($match === null)
            return null;
        else
            return octdec($match);
    }

    function scanFloat()
    {
        $match = $this->scanPattern(self::FLOAT_PATTERN);
        if ($match === null)
            return null;
        else
            return floatval($match);
    }

    function scanIdentifier()
    {
        return $this->scanPattern(self::IDENT_PATTERN);
    }

    function scanVariable()
    {
        return $this->scanPattern(self::VAR_PATTERN);
    }

    function scanLiteralString()
    {
        $this->_validateAtEndOfString();
        $begin = $this->nextLocation();
        $quote = $this->_str[$begin];
        if ($quote != '"' && $quote != "'")
            return null;

        $begin++;
        $s = $this->_str;
        $escape = false;
        $literal = $quote; 
        for ($temp = $begin; $temp < $this->_strlen; $temp++) {
            $c = $s[$temp];
            if ($c === '\\') {
                $literal .= '\\';
                $escape = !$escape;
            } elseif ($c === $quote) {
                $literal .= $quote;
                if ($escape)
                    $escape = false;
                else {
                    $this->_lc = $temp + 1;
                    return $literal;
                }
            } else {
                if ($escape)
                    $escape = false;
                $literal .= $c;
            }
        }
        throw new NorfStringScannerAtEndException();
    }

    function scanStringBeginsWith($begin, $end,
                                  $recursively=true,
                                  $pattern=null,
                                  $includes=true)
    {
        $this->_validateAtEndOfString();
        $baseLc = $lc = $this->nextLocation();
        $beginLen = strlen($begin);
        $endLen = strlen($end);
        $cmp = substr($this->_str, $lc, $beginLen);
        if ($begin !== $cmp)
            return null;

        $lc += $beginLen;
        $count = 1;
        $escape = false;
        while ($count > 0) {
            $lc = $this->nextLocation($lc);
            if ($lc >= $this->_strlen)
                return null;

            if ($this->_str[$lc] == '\\') {
                $escape = true;
                $lc++;
                continue;
            }

            $cmp = substr($this->_str, $lc, $beginLen);
            if ($begin === $cmp) {
                $lc += $beginLen;
                if ($escape)
                    $escape = false;
                else
                    $count++;
                continue;
            }

            $cmp = substr($this->_str, $lc, $endLen);
            if ($end === $cmp) {
                $lc += $endLen;
                if ($escape)
                    $escape = false;
                else
                    $count--;
                continue;
            }

            $escape = false;
            if (preg_match($pattern, $this->_str, $matches, 0, $lc)) {
                $lc += strlen($matches[0]);
            }
        }

        $this->_lc = $lc;
        if ($includes)
            return substr($this->_str, $baseLc, $lc - $baseLc);
        else
            return substr($this->_str, $baseLc + $beginLen,
                          $lc - $baseLc - $endLen - $beginLen);
    }

}


class NorfPHPScannerError extends Exception
{

    static function withToken($tok, $msg)
    {
        $error = new self($msg, $tok->path(), $tok->lineNumber(),
                          $tok->characterColumnNumber());
        $error->_tok =  $tok;
        return $error;
    }

    function __construct($msg, $path=null, $lineNum=null, $charColNum=null)
    {
        parent::__construct($msg);
        $this->_path = $path;
        $this->_lineNum = $lineNum;
        $this->_charColNum = $charColNum;
    }

    function path()
    {
        return $this->_path;
    }

    function lineNumber()
    {
        return $this->_lineNum;
    }

    function characterColumnNumber()
    {
        return $this->_charColNum;
    }

    function token()
    {
        return $this->_tok;
    }

}

