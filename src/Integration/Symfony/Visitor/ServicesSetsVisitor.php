<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Symfony\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ServicesSetsVisitor extends NodeVisitorAbstract
{
    private static array $methodCalls = [
        'lazy',
        'public',
        'autowire',
        'class',
        'factory',
        'private',
        'tag',
    ];

    /**
     * @psalm-var list<array{node: Node\Expr\MethodCall, calls: list<Node\Expr\MethodCall>}>
     */
    private array $serviceSetCalls = [];

    /**
     * @psalm-var list<Node\Expr\MethodCall>
     */
    private array $currentServiceCalls = [];

    private bool $inExpression = false;

    public function enterNode(Node $node): void
    {
        if ($this->inExpression) {
            if ($node instanceof Node\Expr\MethodCall) {
                $name = $node->name instanceof Node\Identifier ? $node->name->name : '';

                if (in_array($name, self::$methodCalls, true)) {
                    $this->currentServiceCalls[] = $node;
                }

                if ('set' === $name) {
                    $this->serviceSetCalls[] = [
                        'node' => $node,
                        'calls' => $this->currentServiceCalls,
                    ];
                    $this->currentServiceCalls = [];
                }

                if ('defaults' === $name) {
                    $this->currentServiceCalls = [];
                }
            } else {
                $this->inExpression = false;
            }
        }

        if ($node instanceof Node\Stmt\Expression) {
            $this->inExpression = true;
        }
    }

    /**
     * @psalm-return list<array{node: Node\Expr\MethodCall, calls: list<Node\Expr\MethodCall>}>
     */
    public function getServiceSetCalls(): array
    {
        return $this->serviceSetCalls;
    }
}
