<?php

namespace JackCompiler\CompileEngine;

use MyCLabs\Enum\Enum;

/**
 * @method static CompilationType START_CLASS()
 * @method static CompilationType CLASS_VAR_DEC()
 * @method static CompilationType KEYWORD()
 * @method static CompilationType SYMBOL()
 * @method static CompilationType IDENTIFIER()
 * @method static CompilationType INTEGER_CONSTANT()
 * @method static CompilationType STRING_CONSTANT()
 * @method static CompilationType STATEMENTS()
 * @method static CompilationType LET_STATEMENT()
 * @method static CompilationType IF_STATEMENT()
 * @method static CompilationType WHILE_STATEMENT()
 * @method static CompilationType DO_STATEMENT()
 * @method static CompilationType RETURN_STATEMENT()
 * @method static CompilationType SUBROUTINE_DEC()
 * @method static CompilationType PARAMETER_LIST()
 * @method static CompilationType VAR_DEC()
 * @method static CompilationType TERM()
 * @method static CompilationType EXPRESSION()
 * @method static CompilationType EXPRESSION_LIST()
 * @method static CompilationType SUBROUTINE_CALL()
 * @method static CompilationType SUBROUTINE_BODY()
 * @method static CompilationType VARS()
 * @extends Enum<string>
 */
class CompilationType extends Enum
{
    private const START_CLASS = 'class';
    private const CLASS_VAR_DEC = 'classVarDec';
    private const KEYWORD = 'keyword';
    private const SYMBOL = 'symbol';
    private const IDENTIFIER = 'identifier';
    private const INTEGER_CONSTANT = 'intConstant';
    private const STRING_CONSTANT = 'stringConstant';
    private const STATEMENTS = 'statements';
    private const LET_STATEMENT = 'letStatement';
    private const IF_STATEMENT = 'ifStatement';
    private const WHILE_STATEMENT = 'whileStatement';
    private const DO_STATEMENT = 'doStatement';
    private const RETURN_STATEMENT = 'returnStatement';
    private const SUBROUTINE_DEC = 'subroutineDec';
    private const PARAMETER_LIST = 'parameterList';
    private const VAR_DEC = 'varDec';
    private const TERM = 'term';
    private const EXPRESSION = 'expression';
    private const EXPRESSION_LIST = 'expressionList';
    private const SUBROUTINE_CALL = 'subroutineCall';
    private const SUBROUTINE_BODY = 'subroutineBody';
    private const VARS = 'vars';
}
