<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Symfony\Model;

use PhpParser\Node;
use SoureCode\PhpObjectModel\Model\AbstractModel;

/**
 * @extends AbstractModel<Node\Expr\MethodCall>
 */
final class ServicesSetCallModel extends AbstractModel
{
    public function __construct(Node\Expr\MethodCall $node)
    {
        parent::__construct($node);
    }

    public function getName(): string
    {
        if ($this->node->name instanceof Node\Identifier) {
            return $this->node->name->name;
        }

        throw new \RuntimeException('Could not get name of service call.');
    }

    public function getArgs(): array
    {
        return $this->node->args;
    }
}
