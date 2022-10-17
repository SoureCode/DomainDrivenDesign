<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Files;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use LogicException;
use PhpParser\Node;
use SoureCode\DomainDrivenDesign\Area\AbstractAreaFile;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\Doctrine\DoctrineHelper;
use SoureCode\PhpObjectModel\Model\ArgumentModel;
use SoureCode\PhpObjectModel\Model\AttributeModel;
use SoureCode\PhpObjectModel\Model\ClassMethodModel;
use SoureCode\PhpObjectModel\Model\ClassModel;
use SoureCode\PhpObjectModel\Model\DeclareModel;
use SoureCode\PhpObjectModel\Model\ParameterModel;
use SoureCode\PhpObjectModel\Model\PropertyModel;
use SoureCode\PhpObjectModel\Type\AbstractType;
use SoureCode\PhpObjectModel\Type\ClassType;
use SoureCode\PhpObjectModel\Type\StringType;
use SoureCode\PhpObjectModel\Value\ClassConstValue;
use SoureCode\PhpObjectModel\Value\StringValue;

final class ValueObject extends AbstractAreaFile
{
    private DoctrineHelper $doctrineHelper;

    public function __construct(DoctrineHelper $doctrineHelper, AreaInterface $area, string $name)
    {
        parent::__construct($area, $name);

        $this->doctrineHelper = $doctrineHelper;

        $file = $this->getClassFile();
        $file->setDeclare((new DeclareModel())->setStrictTypes(true))
            ->setNamespace($this->getNamespace())
            ->addUse('Doctrine\\ORM\\Mapping', 'ORM');

        $class = (new ClassModel($name))
            ->setFinal(true);

        $file->setClass($class);

        $columnName = $doctrineHelper->getPotentialTableName($name);

        $columnAttribute = new AttributeModel(Column::class);
        $columnAttribute->setArgument(
            new ArgumentModel(
                'name',
                new StringValue(
                    $this->doctrineHelper->isKeyword($name) ? '"' . $columnName . '"' : $columnName
                )
            )
        );

        $property = (new PropertyModel('value'))
            ->setReadonly(true)
            ->setPublic()
            ->addAttribute($columnAttribute);

        $constructor = new ClassMethodModel('__construct');
        $constructor->setPublic();

        $class
            ->addAttribute(Embeddable::class)
            ->addProperty($property)
            ->addMethod($constructor);

        $this->setType(new StringType());
    }

    public function getColumnName(): string
    {
        $classFile = $this->getClassFile();
        $class = $classFile->getClass();
        $property = $class->getProperty('value');
        $attribute = $property->getAttribute(Column::class);
        $argument = $attribute->getArgument('name');
        $value = $argument->getValue();

        if ($value instanceof StringValue) {
            return $value->getValue();
        }

        throw new LogicException('Value is not a string.');
    }

    public function setColumnName(string $columnName): void
    {
        $classFile = $this->getClassFile();
        $class = $classFile->getClass();
        $property = $class->getProperty('value');
        $attribute = $property->getAttribute(Column::class);
        $argument = $attribute->getArgument('name');

        $argument->setValue(new StringValue($columnName));
    }

    public function getType(): ?AbstractType
    {
        return $this->getClassFile()->getClass()->getProperty('value')->getType();
    }

    public function setType(AbstractType $type, bool $passthrough = true): self
    {
        $class = $this->getClassFile()->getClass();

        $property = $class->getProperty('value');
        $constructor = $class->getMethod('__construct');

        $property->setType($type);

        if ($passthrough && !($type instanceof ClassType)) {
            $constructor->setParameters([
                new ParameterModel('value', $type),
            ]);

            $constructor->setStatements(
                [
                    new Node\Expr\Assign(
                        new Node\Expr\PropertyFetch(
                            new Node\Expr\Variable('this'),
                            'value'
                        ),
                        new Node\Expr\Variable('value')
                    ),
                ]
            );
        } else {
            $constructor->setParameters([]);
            $constructor->setStatements([
                    new Node\Expr\Assign(
                        new Node\Expr\PropertyFetch(
                            new Node\Expr\Variable('this'),
                            'value'
                        ),
                        new Node\Expr\New_(
                            new Node\Name($type->getClassName()->getShortName()),
                        )
                    ),
            ]);
        }

        $attribute = $property->getAttribute(Column::class);

        if (!$this->doctrineHelper->canColumnTypeBeInferredByPropertyType($type)) {
            $constName = $this->doctrineHelper->getTypeConstant($type);

            if (null === $constName) {
                throw new \LogicException('Could not find constant for type ' . $type::class);
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
