grammar search;

start : STRING* condition_expression ;

condition_expression :
    condition # cond
    | condition_expression ('AND'|'And'|'and') condition_expression # andCondExpr
    | condition_expression ('OR'|'Or'|'or') condition_expression # orCondExpr
    | '(' condition_expression ')' # wrapCondExpr
    ;

condition :
    KEYS STRING
    ;

STRING :
    '"' .*?  '"'
    ;

KEYS :
    'name:' | 'テストケース名:'
    | 'precondition:' | '前提条件:'
    | 'purpose:' | '目的:'
    | 'expect_result:' | '期待結果:'
    | 'tags:' | 'タグ:'
    ;

WS : [ \t\r\n]+ -> skip ;