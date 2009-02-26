<?php

class NorfPHPToken
{

    const TEXT_TAG = 'Text';
    const BEGIN_PHP_TAG = 'BeginProgram';   // <?php
    const END_PHP_TAG = 'EndProgram';       // ? >

    const IDENT_TAG = 'Identifier';
    const VAR_TAG = 'Variable';             // $...

    const LPAREN_TAG = 'LeftParenthesis';   // (
    const RPAREN_TAG = 'RightParenthesis';  // )
    const LBRACK_TAG = 'LeftBracket';       // [
    const RBRACK_TAG = 'RightBracket';      // ]
    const LBRACE_TAG = 'LeftBrace';         // {
    const RBRACE_TAG = 'RightBrace';        // }
    const EQ_TAG = 'Equal';                 // ==
    const NE_TAG = 'NotEqual';              // !=
    const IEQ_TAG = 'IdentityEqual';        // ===
    const INE_TAG = 'IdentityNotEqual';     // !==
    const GT_TAG = 'GreaterThan';           // >
    const LT_TAG = 'LessThan';              // <
    const GE_TAG = 'GreaterThanOrEqual';    // >=
    const LE_TAG = 'LessThanOrEqual';       // <=
    const INC_TAG = 'Increment';            // ++
    const DEC_TAG = 'Decrement';            // --
    const ADD_TAG = 'Add';                  // +
    const SUB_TAG = 'Subtract';             // -
    const MUL_TAG = 'Multiple';             // *
    const DIV_TAG = 'Divide';               // /
    const MOD_TAG = 'Modulo';               // %
    const NOT_TAG = 'Not';                  // ~
    const AND_TAG = 'And';                  // &
    const OR_TAG = 'Or';                    // |
    const XOR_TAG = 'Xor';                  // ^
    const LSHIFT_TAG = 'LeftShift';         // >>
    const RSHIFT_TAG = 'RightShift';        // <<
    const LNOT_TAG = 'LogicalNot';          // !
    const LAND1_TAG = 'LogicalAnd1';        // &&
    const LAND2_TAG = 'LogicalAnd2';        // and
    const LOR1_TAG = 'LogicalOr1';          // ||
    const LOR2_TAG = 'LogicalOr2';          // or
    const LXOR_TAG = 'LogicalXor';          // xor
    const CONCATE_TAG = 'Concate';          // .
    const COMMA_TAG = 'Comma';              // ,
    const BQUOTE_TAG = 'BackQuote';         // `
    const COLON_TAG = 'Colon';              // :
    const SEP_TAG = 'Separator';            // ::
    const TERM_TAG = 'Term';                // ;
    const AT_TAG = 'At';                    // @
    const Q_TAG = 'Question';               // ?
    const DOLLAR_TAG = 'Dollar';            // $
    const REF_TAG = 'Reference';            // ->
    const ASSOC_TAG = 'Association';        // =>

    const ASSIGN_TAG = 'Assign';                    // =
    const ADD_ASSIGN_TAG = 'AddAssign';             // +=
    const SUB_ASSIGN_TAG = 'SubtractAssign';        // -=
    const MUL_ASSIGN_TAG = 'MultipleAssign';        // *=
    const DIV_ASSIGN_TAG = 'DivideAssign';          // /=
    const MOD_ASSIGN_TAG = 'ModuloAssign';          // %=
    const NOT_ASSIGN_TAG = 'NotAssign';             // ~=
    const AND_ASSIGN_TAG = 'AndAssign';             // &=
    const OR_ASSIGN_TAG = 'OrAssign';               // |=
    const XOR_ASSIGN_TAG = 'XorAssign';             // ^=
    const LSHIFT_ASSIGN_TAG = 'LeftShiftAssign';    // >>=
    const RSHIFT_ASSIGN_TAG = 'RightShiftAssign';   // <<=
    const CONCATE_ASSIGN_TAG = 'ConcateAssign';     // .=

    const STRING_TAG = 'String';
    const INT_TAG = 'Integer';
    const FLOAT_TAG = 'Float';
    const NULL_TAG = 'Null';
    const TRUE_TAG = 'True';
    const FALSE_TAG = 'False';
    const DOCTEST_TAG = 'DocTest';

    const ABSTRACT_TAG = 'Abstract';
    const CLASS_TAG = 'Class';
    const EXTENDS_TAG = 'Extends';
    const FINAL_TAG = 'Final';
    const FUNCTION_TAG = 'Function';
    const RETURN_TAG = 'Return';
    const ELSE_TAG = 'Else';
    const ELSEIF_TAG = 'ElseIf';
    const DEFAULT_TAG = 'Default';
    const INTERFACE_TAG = 'Interface';
    const IMPLEMENTS_TAG = 'Implements';
    const IF_TAG = 'If';
    const PUBLIC_TAG = 'Public';
    const PROTECTED_TAG = 'Protected';
    const PRIVATE_TAG = 'Private';
    const FOR_TAG = 'For';
    const FOREACH_TAG = 'Foreach';
    const PARENT_TAG = 'Parent';
    const STATIC_TAG = 'Static';
    const CONST_TAG = 'Const';
    const SELF_TAG = 'Self';

    const DOCTEST_DIR_TAG = 'DocTestDirective';

    function __construct($path, $line, $col, $tag, $value=null)
    {
        $this->_path = $path;
        $this->_line = $line;
        $this->_col = $col;
        $this->_tag = $tag;
        $this->_value = $value;
    }

    function path()
    {
        return $this->_path;
    }

    function lineNumber()
    {
        return $this->_line;
    }

    function characterColumnNumber()
    {
        return $this->_col;
    }

    function tag()
    {
        return $this->_tag;
    }

    function isTag($tag)
    {
        return $this->_tag === $tag;
    }

    function notTag($tag)
    {
        return $this->_tag !== $tag;
    }

    function value()
    {
        return $this->_value;
    }

    function __toString()
    {
        return '<' . get_class($this) . ': tag=' . $this->_tag .
            ', value=' . $this->_value . '>';
    }

}

