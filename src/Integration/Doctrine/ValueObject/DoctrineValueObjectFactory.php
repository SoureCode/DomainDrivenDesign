<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Doctrine\ValueObject;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\DoctrineHelper;
use SoureCode\DomainDrivenDesign\ValueObject\ValueObject;
use SoureCode\DomainDrivenDesign\ValueObject\ValueObjectFactoryInterface;
use SoureCode\PhpObjectModel\Model\ArgumentModel;
use SoureCode\PhpObjectModel\Model\AttributeModel;
use SoureCode\PhpObjectModel\Value\StringValue;

class DoctrineValueObjectFactory implements ValueObjectFactoryInterface
{
    protected ValueObjectFactoryInterface $valueObjectFactory;

    protected DoctrineHelper $doctrineHelper;

    public function __construct(ValueObjectFactoryInterface $valueObjectFactory, DoctrineHelper $doctrineHelper)
    {
        $this->valueObjectFactory = $valueObjectFactory;
        $this->doctrineHelper = $doctrineHelper;
    }

    public function create(AreaInterface $area, string $name): ValueObject
    {
        /**
         * @var DoctrineValueObject $valueObject
         */
        $valueObject = $this->valueObjectFactory->create($area, $name);
        $valueObject->setDoctrineHelper($this->doctrineHelper);
        $file = $valueObject->getClassFile();

        $file->addUse('Doctrine\\ORM\\Mapping', 'ORM');

        $class = $file->getClass();

        $class->addAttribute(Embeddable::class);

        $columnName = $this->doctrineHelper->getPotentialColumnName($name);
        $escapedColumnName = $this->doctrineHelper->escapeName($columnName);

        $columnAttribute = new AttributeModel(Column::class);
        $columnAttribute->setArgument(
            new ArgumentModel(
                'name',
                new StringValue($escapedColumnName)
            )
        );

        $property = $class->getProperty('value');
        $property->addAttribute($columnAttribute);

        return $valueObject;
    }
}
