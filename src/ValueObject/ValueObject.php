<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\ValueObject;

use RuntimeException;
use SoureCode\DomainDrivenDesign\Area\AbstractAreaFile;
use SoureCode\PhpObjectModel\Comparer\AbstractNodeComparer;
use SoureCode\PhpObjectModel\Model\ClassMethodModel;
use SoureCode\PhpObjectModel\Model\ClassModel;
use SoureCode\PhpObjectModel\Model\ParameterModel;
use SoureCode\PhpObjectModel\Type\AbstractType;
use SoureCode\PhpObjectModel\Type\ClassType;
use SoureCode\PhpObjectModel\Value\NullValue;

class ValueObject extends AbstractAreaFile
{
    public function getType(): ?AbstractType
    {
        return $this->getClass()->getProperty('value')->getType();
    }

    /**
     * Returns true if the value param in the constructor will be passed through to the property, false otherwise.
     */
    public function isPass(): bool
    {
        $class = $this->getClass();
        $constructor = $class->getMethod('__construct');

        return AbstractNodeComparer::compareNodes(
            $constructor->getNode(),
            $this->createPassConstructorMethod($class)->getNode()
        );
    }

    /**
     * Pass the value param in the constructor to the property.
     */
    public function setPass(): self
    {
        $class = $this->getClass();

        if ($class->hasMethod('__construct')) {
            $class->removeMethod('__construct');
        }

        $class->addMethod($this->createPassConstructorMethod($class));

        return $this;
    }

    /**
     * Initiate the property in the constructor.
     */
    public function setConstruct(): self
    {
        $class = $this->getClass();

        if ($class->hasMethod('__construct')) {
            $class->removeMethod('__construct');
        }

        $class->addMethod($this->createConstructConstructorMethod($class));

        return $this;
    }

    public function isConstruct(): bool
    {
        $class = $this->getClass();
        $constructor = $class->getMethod('__construct');

        return AbstractNodeComparer::compareNodes(
            $constructor->getNode(),
            $this->createConstructConstructorMethod($class)->getNode()
        );
    }

    /**
     * Pass the value param in the constructor to the property if null initiate the property in the constructor.
     */
    public function setPassOrConstruct(): self
    {
        $class = $this->getClass();

        if ($class->hasMethod('__construct')) {
            $class->removeMethod('__construct');
        }

        $class->addMethod($this->createPassOrConstructConstructorMethod($class));

        return $this;
    }

    public function isPassOrConstruct(): bool
    {
        $class = $this->getClass();
        $constructor = $class->getMethod('__construct');

        return AbstractNodeComparer::compareNodes(
            $constructor->getNode(),
            $this->createPassOrConstructConstructorMethod($class)->getNode()
        );
    }

    public function setType(AbstractType $type): self
    {
        $class = $this->getClassFile()->getClass();
        $property = $class->getProperty('value');

        $currentType = $property->getType();
        $isPass = $this->isPass();
        $isConstruct = $currentType instanceof ClassType && $this->isConstruct();
        $isPassOrConstruct = $currentType instanceof ClassType && $this->isPassOrConstruct();

        $property->setType($type);

        // just set the constructor again to update the type, performance?
        if ($isPass) {
            $this->setPass();
        } elseif ($isConstruct) {
            $this->setConstruct();
        } elseif ($isPassOrConstruct) {
            $this->setPassOrConstruct();
        }

        return $this;
    }

    private function createPassConstructorMethod(ClassModel $class): ClassMethodModel
    {
        $constructor = new ClassMethodModel('__construct');
        $parameter = new ParameterModel('value', $this->getType());

        $constructor->setParameters([$parameter]);

        $property = $class->getProperty('value');
        $assign = $property->assignTo($parameter);

        $constructor->setStatements([$assign]);

        return $constructor;
    }

    private function createConstructConstructorMethod(ClassModel $class): ClassMethodModel
    {
        $constructor = new ClassMethodModel('__construct');

        $type = $this->getType();

        if (!$type instanceof ClassType) {
            throw new RuntimeException('Type must be a class type to be able to construct.');
        }

        $constructor->setParameters([]);

        $property = $class->getProperty('value');
        $assign = $property->assignTo($type->toNewNode());

        $constructor->setStatements([$assign]);

        return $constructor;
    }

    private function createPassOrConstructConstructorMethod(ClassModel $class): ClassMethodModel
    {
        $constructor = new ClassMethodModel('__construct');

        $type = $this->getType();

        if (!$type instanceof ClassType) {
            throw new RuntimeException('Type must be a class type to be able to construct.');
        }

        // (?Type $value = null)
        $parameter = new ParameterModel(
            'value',
            (clone $type)->setNullable(true),
        );

        $parameter->setDefault(new NullValue());

        $constructor->setParameters([
            $parameter,
        ]);

        $property = $class->getProperty('value');
        $assign = $property->assignToOrNew($parameter);

        // $this->value = $value ?? new Value();
        $constructor->setStatements([$assign]);

        return $constructor;
    }
}
