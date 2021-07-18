<?php

class CustomVisitor extends searchBaseVisitor {
    public $search;
    public $exprs;
    public $tags;
    public $model;

    const KEY_NAME = ['name:', 'テストケース名:'];
    const KEY_PRECONDITION = ['precondition:', '前提条件:'];
    const KEY_PURPOSE = ['purpose:', '目的:'];
    const KEY_EXPECT_RESULT = ['expect_result:', '期待結果:'];
    const KEY_TAGS = ['tags:', 'タグ:'];

    function __construct($model) {
        $this->model = $model;
        $this->search = '';
        $this->tags = [];
        $this->exprs = [];
    }

    public function visitStart(Context\StartContext $context)
    {   $count = $context->getChildCount();
        if ($count == 0) {
            return;
        }
        $expr_node = $context->getChild(0);
        if ($count > 1) {
            $expr_node = $context->getChild(1);
            $this->search = $context->getChild(0)->getText();
        }
        $this->exprs = $this->visit($expr_node);
        return [
            'search' => $this->removeQuote($this->search),
            'query' => $this->exprs,
            'tags' => $this->tags
        ];
    }

    public function buildExpr($left, $right, $operator) {
        $result = [];
        if (!empty($left))
            $result[] = $left;
        if (!empty($right))
            $result[] = $right;
        if (empty($result))
            return [];
        return [$operator => $result];
    }

    public function visitAndCondExpr(Context\AndCondExprContext $context)
    {
        $operator = $context->getChild(1)->getText();
        $left = $this->visit($context->getChild(0));
        $this->visit($context->getChild(1));
        $right = $this->visit($context->getChild(2));
        return $this->buildExpr($left, $right, $operator);
    }

    public function visitOrCondExpr(Context\OrCondExprContext $context)
    {
        $operator = $context->getChild(1)->getText();
        $left = $this->visit($context->getChild(0));
        $this->visit($context->getChild(1));
        $right = $this->visit($context->getChild(2));
        return $this->buildExpr($left, $right, $operator);
    }

    public function visitWrapCondExpr(Context\WrapCondExprContext $context)
    {
        return $this->visit($context->getChild(1));
    }

    public function visitCondition(Context\ConditionContext $context)
    {
        $key = $context->KEYS()->getText();
        $val = $context->STRING()->getText();
        if (empty($key))
            return [];

        if (in_array($key,self::KEY_TAGS)) {
            $this->tags[] = $this->removeQuote($val);
            return [];
        }
        $query = $this->getQuery($key, $val);
        if (empty($query))
            return [];
        return [$query];
    }

    public function removeQuote($val) {
        if (preg_match('/"([^"]+)"/', $val, $m)) {
            return $m[1];
        }
        return $val;
    }

    public function getQuery($key, $val) {
        $mkey = $this->mapping($key);
        if (empty($mkey))
            return;
        $field = substr($mkey, 0, -1);
        $val = $this->removeQuote($val);
        return $this->model . "." . $field . " LIKE '%{$val}%'";
    }

    public function mapping($key) {
        if (in_array($key, self::KEY_NAME)) {
            return self::KEY_NAME[0];
        }
        if (in_array($key, self::KEY_PRECONDITION)) {
            return self::KEY_PRECONDITION[0];
        }
        if (in_array($key, self::KEY_PURPOSE)) {
            return self::KEY_PURPOSE[0];
        }
        if (in_array($key, self::KEY_EXPECT_RESULT)) {
            return self::KEY_EXPECT_RESULT[0];
        }
        return;
    }
}