<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Doctrine\ValueObject;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use SoureCode\DomainDrivenDesign\Area\AreaInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectFactoryInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectInterface;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\DoctrineHelper;
use SoureCode\PhpObjectModel\Model\ArgumentModel;
use SoureCode\PhpObjectModel\Model\AttributeModel;
use SoureCode\PhpObjectModel\Model\UseModel;
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

    public function create(AreaInterface $area, string $name): ValueObjectInterface
    {
        /**
         * @var DoctrineValueObject $valueObject
         */
        $valueObject = $this->valueObjectFactory->create($area, $name);
        $valueObject->setDoctrineHelper($this->doctrineHelper);
        $file = $valueObject->getFile();

        $useModel = new UseModel('Doctrine\\ORM\\Mapping', 'ORM');

        if (!$file->hasUse($useModel)) {
            $file->addUse($useModel);
        }

        $class = $file->getClass();

        if (!$class->hasAttribute(Embeddable::class)) {
            $class->addAttribute(Embeddable::class);
        }

        $property = $class->getProperty('value');

        if (!$property->hasAttribute(Column::class)) {
            $columnName = $this->doctrineHelper->getPotentialColumnName($name);
            $escapedColumnName = $this->doctrineHelper->escapeName($columnName);

            $columnAttribute = new AttributeModel(Column::class);
            $columnAttribute->setArgument(
                new ArgumentModel(
                    'name',
                    new StringValue($escapedColumnName)
                )
            );

            $property->addAttribute($columnAttribute);
        }

        return $valueObject;
    }
}
