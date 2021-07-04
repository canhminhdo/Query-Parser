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
    'name:'
    | 'precondition:'
    | 'purpose:'
    | 'expect_result:'
    | 'tags:'
    ;

WS : [ \t\r\n]+ -> skip ;