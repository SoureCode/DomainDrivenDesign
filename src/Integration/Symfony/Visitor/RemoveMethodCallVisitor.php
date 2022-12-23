<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Symfony\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class RemoveMethodCallVisitor extends NodeVisitorAbstract
{
    private Node\Expr\MethodCall $node;

    public function __construct(Node\Expr\MethodCall $node)
    {
        $this->node = $node;
    }

    public function leaveNode(Node $node)
    {
        if ($node === $this->node) {
            return $node->var;
        }
    }
}
