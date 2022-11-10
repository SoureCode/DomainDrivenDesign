<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Doctrine\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObject;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\DoctrineHelper;
use SoureCode\PhpObjectModel\Model\ArgumentModel;
use SoureCode\PhpObjectModel\Type\AbstractType;
use SoureCode\PhpObjectModel\Type\ClassType;
use SoureCode\PhpObjectModel\Value\ClassConstValue;
use SoureCode\PhpObjectModel\Value\StringValue;

class DoctrineValueObject extends ValueObject
{
    private ?DoctrineHelper $doctrineHelper = null;

    public function getColumnName(): string
    {
        $class = $this->getClass();
        $property = $class->getProperty('value');
        $attribute = $property->getAttribute(Column::class);
        $argument = $attribute->getArgument('name');
        $value = $argument->getValue();

        if ($value instanceof StringValue) {
            return $value->getValue();
        }

        throw new \LogicException('Value is not a string.');
    }

    public function setColumnName(string $columnName): DoctrineValueObject
    {
        $class = $this->getClass();
        $property = $class->getProperty('value');
        $attribute = $property->getAttribute(Column::class);
        $argument = $attribute->getArgument('name');

        $argument->setValue(new StringValue($columnName));

        return $this;
    }

    public function setDoctrineHelper(DoctrineHelper $doctrineHelper): void
    {
        $this->doctrineHelper = $doctrineHelper;
    }

    public function setType(AbstractType $type): self
    {
        parent::setType($type);

        if ($this->doctrineHelper) {
            $class = $this->getClass();
            $property = $class->getProperty('value');
            $attribute = $property->getAttribute(Column::class);

            if ($this->doctrineHelper->canColumnTypeBeInferredByPropertyType($type)) {
                if ($attribute->hasArgument('type')) {
                    $attribute->removeArgument('type');
                }

                return $this;
            }

            $constName = $this->doctrineHelper->getTypeConstant($type);

            if (null === $constName) {
                // just set the type itself
                if ($type instanceof ClassType) {
                    $attribute->setArgument(
                        new ArgumentModel(
                            'type',
                            new ClassConstValue($type->getClassName()),
                        )
                    );

                    return $this;
                }

                throw new \LogicException('Type is not supported.');
            }

            $value = new ClassConstValue(Types::class, $constName);

            if ('uuid' === $constName || 'ulid' === $constName) {
                $value = new StringValue($constName);
            }

            $attribute->setArgument(
                new ArgumentModel(
                    'type',
                    $value,
                )
            );
        }

        return $this;
    }
}
