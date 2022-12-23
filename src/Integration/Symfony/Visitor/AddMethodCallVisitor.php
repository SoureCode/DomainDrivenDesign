<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Symfony\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class AddMethodCallVisitor extends NodeVisitorAbstract
{
    private Node\Expr\MethodCall $methodCall;

    private Node\Expr\MethodCall $targetNode;

    public function __construct(Node\Expr\MethodCall $targetNode, Node\Expr\MethodCall $methodCall)
    {
        $this->targetNode = $targetNode;
        $this->methodCall = $methodCall;
    }

    public function leaveNode(Node $node)
    {
        if ($node === $this->targetNode) {
            // $this->methodCall->var = $node;

            return $this->methodCall;
        }
    }
}
