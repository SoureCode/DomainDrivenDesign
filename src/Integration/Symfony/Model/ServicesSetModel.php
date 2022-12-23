<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Symfony\Model;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\VariadicPlaceholder;
use PhpParser\NodeTraverser;
use SoureCode\DomainDrivenDesign\Integration\Symfony\File\ServicesClosureFile;
use SoureCode\DomainDrivenDesign\Integration\Symfony\Visitor\AddMethodCallVisitor;
use SoureCode\DomainDrivenDesign\Integration\Symfony\Visitor\RemoveMethodCallVisitor;
use SoureCode\PhpObjectModel\Model\AbstractModel;
use SoureCode\PhpObjectModel\Node\NodeManipulator;
use SoureCode\PhpObjectModel\ValueObject\ClassName;

/**
 * Limitation: Does not support inheritance from default calls.
 *
 * @extends AbstractModel<Node\Expr\MethodCall>
 */
final class ServicesSetModel extends AbstractModel
{
    private ServicesClosureFile $closureFile;

    /**
     * @var ServicesSetCallModel[]
     */
    private array $calls;

    /**
     * @param ServicesSetCallModel[] $calls
     */
    public function __construct(ServicesClosureFile $closureFile, Node\Expr\MethodCall $node, array $calls = [])
    {
        parent::__construct($node);

        $this->closureFile = $closureFile;
        $this->calls = $calls;
    }

    public function getId(): ClassName
    {
        $args = $this->node->getArgs();

        if (0 === count($args)) {
            throw new \RuntimeException('Could not get id of service set.');
        }

        $arg = $args[0];

        return ClassName::fromString(NodeManipulator::resolveArgument($arg));
    }

    private function removeCall(string $name): self
    {
        $calls = array_filter($this->calls, static function (ServicesSetCallModel $call) use ($name) {
            return $name === $call->getName();
        });

        if (count($calls) > 0) {
            // remove call nodes
            foreach ($calls as $call) {
                $this->removeCallNode($this->closureFile->getClosure()->getNode(), $call->getNode());
            }

            // remove call models
            $this->calls = array_filter($this->calls, static function (ServicesSetCallModel $call) use ($name) {
                return $name !== $call->getName();
            });
        }

        return $this;
    }

    private function removePublicAndPrivateCalls(): self
    {
        return $this->removeCall('public')->removeCall('private');
    }

    public function public(): self
    {
        return $this->removePublicAndPrivateCalls()->addMethodCall('public');
    }

    public function isPublic(): bool
    {
        return $this->hasCall('public');
    }

    public function isLazy(): bool
    {
        return $this->hasCall('lazy');
    }

    public function lazy(): self
    {
        return $this->removeCall('lazy')->addMethodCall('lazy');
    }

    public function isPrivate(): bool
    {
        return $this->hasCall('private');
    }

    public function hasCall(string $name): bool
    {
        foreach ($this->calls as $call) {
            if ($name === $call->getName()) {
                return true;
            }
        }

        return false;
    }

    public function getCall(string $name): ?ServicesSetCallModel
    {
        foreach ($this->calls as $call) {
            if ($name === $call->getName()) {
                return $call;
            }
        }

        return null;
    }

    public function private(): self
    {
        return $this->removePublicAndPrivateCalls()->addMethodCall('private');
    }

    /**
     * @param array<Arg|VariadicPlaceholder> $args Arguments
     */
    private function addMethodCall(string $name, array $args = []): self
    {
        $first = $this->calls[0] ?? null;
        $node = $first ? $first->getNode() : $this->node;

        $factory = new BuilderFactory();
        $methodCall = $factory->methodCall($node, $name, $args);
        $methodCallModel = new ServicesSetCallModel($methodCall);
        $methodCallModel->setFile($this->file);

        $this->addCallNode($this->closureFile->getClosure()->getNode(), $node, $methodCallModel->getNode());
        $this->calls[] = $methodCallModel;

        return $this;
    }

    /**
     * @psalm-param Node|Node[] $nodes
     */
    private function removeCallNode(Node|array $nodes, Node\Expr\MethodCall $node): self
    {
        if (!is_array($nodes)) {
            $nodes = [$nodes];
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new RemoveMethodCallVisitor($node));

        $traverser->traverse($nodes);

        return $this;
    }

    /**
     * @psalm-param Node|Node[] $nodes
     */
    private function addCallNode(Node|array $nodes, Node\Expr\MethodCall $node, Node\Expr\MethodCall $methodCall): self
    {
        if (!is_array($nodes)) {
            $nodes = [$nodes];
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new AddMethodCallVisitor($node, $methodCall));

        $traverser->traverse($nodes);

        return $this;
    }

    public function isAutowired(): bool
    {
        return $this->hasCall('autowire');
    }

    public function autowire(): self
    {
        return $this->removeCall('autowire')->addMethodCall('autowire');
    }

    public function isAutoconfigured(): bool
    {
        return $this->hasCall('autoconfigure');
    }

    public function autoconfigure(): self
    {
        return $this->removeCall('autoconfigure')->addMethodCall('autoconfigure');
    }

    public function hasArgs(): bool
    {
        return $this->hasCall('args');
    }

    /**
     * @param array<Arg|VariadicPlaceholder> $args Arguments
     */
    public function args(array $args): self
    {
        $this->removeCall('args');
        $this->addMethodCall('args', $args);

        return $this;
    }

    public function hasClass(): bool
    {
        return $this->hasCall('class');
    }

    public function class(ClassName $class): self
    {
        $this->removeCall('class');

        $useName = $this->closureFile->resolveUseName($class);

        $this->node->args[1] = new Arg(
            new Node\Expr\ClassConstFetch(
                $useName,
                'class'
            )
        );

        return $this;
    }

    public function getServiceClass(): ClassName
    {
        $args = $this->node->getArgs();

        if (2 === count($args)) {
            $arg = $args[1];

            return ClassName::fromString(NodeManipulator::resolveArgument($arg));
        }

        if ($this->hasCall('class')) {
            $call = $this->getCall('class');

            if ($call) {
                $args = $call->getArgs();

                if (1 <= count($args)) {
                    /**
                     * @var Node\Arg $arg
                     */
                    $arg = $args[0];

                    return ClassName::fromString(NodeManipulator::resolveArgument($arg));
                }
            }
        }

        throw new \RuntimeException('Could not get class of service set.');
    }
}
