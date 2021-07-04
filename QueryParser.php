<?php
include "vendor/autoload.php";
include "searchLexer.php";
include "searchParser.php";
include "searchVisitor.php";
include "searchBaseVisitor.php";
include "CustomVisitor.php";

use Antlr\Antlr4\Runtime\CommonTokenStream;
use Antlr\Antlr4\Runtime\Error\Listeners\DiagnosticErrorListener;
use Antlr\Antlr4\Runtime\InputStream;

class QueryParser {

    public static function parse($query, $model) {
        $input = InputStream::fromString($query);
        $lexer = new searchLexer($input);
        $tokens = new CommonTokenStream($lexer);
        $parser = new searchParser($tokens);
        $parser->addErrorListener(new DiagnosticErrorListener());
        $parser->setBuildParseTree(true);
        $tree = $parser->start();
        $visitor = new CustomVisitor($model);
        $result = $visitor->visit($tree);
        return $result;
    }
}

