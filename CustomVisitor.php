<?php

class CustomVisitor extends searchBaseVisitor {
    public $search;
    public $exprs;
    public $tags;
    public $model;

    const KEY_TAG = 'tags:';

    function __construct($model) {
        $this->model = $model;
        $this->search = '';
        $this->tags = '';
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
            'tags' => $this->removeQuote($this->tags)
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

        if ($key == self::KEY_TAG) {
            $this->tags = $val;
            return [];
        }
        return [$this->getQuery($key, $val)];
    }

    public function removeQuote($val) {
        if (preg_match('/"([^"]+)"/', $val, $m)) {
            return $m[1];
        }
        return $val;
    }

    public function getQuery($key, $val) {
        $field = substr($key, 0, -1);
        $val = $this->removeQuote($val);
        return $this->model . "." . $field . " LIKE '%{$val}%'";
    }
}