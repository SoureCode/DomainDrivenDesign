<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use SoureCode\PhpObjectModel\Type\AbstractType;
use SoureCode\PhpObjectModel\Type\ArrayType;
use SoureCode\PhpObjectModel\Type\BooleanType;
use SoureCode\PhpObjectModel\Type\ClassType;
use SoureCode\PhpObjectModel\Type\FloatType;
use SoureCode\PhpObjectModel\Type\IntegerType;
use SoureCode\PhpObjectModel\Type\ObjectType;
use SoureCode\PhpObjectModel\Type\StringType;
use SoureCode\PhpObjectModel\ValueObject\ClassName;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;

/**
 * @copyright https://github.com/symfony/maker-bundle/blob/e607f129d29a6c1e9a9e1ef3d229d653311d58f3/src/Doctrine/DoctrineHelper.php
 */
class DoctrineHelper
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function escapeName(string $name): string
    {
        if ($this->isKeyword($name)) {
            /**
             * @var Connection $connection
             */
            $connection = $this->registry->getConnection();
            $platform = $connection->getDatabasePlatform();

            return $platform->quoteSingleIdentifier($name);
        }

        return $name;
    }

    /**
     * @param class-string|ClassName $className
     */
    public function getPotentialTableName(ClassName|string $className): string
    {
        $entityManager = $this->registry->getManager();

        if (!$entityManager instanceof EntityManagerInterface) {
            throw new \RuntimeException('ObjectManager is not an EntityManagerInterface.');
        }

        $className = $className instanceof ClassName ? $className->getName() : $className;

        return $entityManager
            ->getConfiguration()
            ->getNamingStrategy()
            ->classToTableName($className);
    }

    public function getPotentialColumnName(ClassName|string $propertyName): string
    {
        $entityManager = $this->registry->getManager();

        if (!$entityManager instanceof EntityManagerInterface) {
            throw new \RuntimeException('ObjectManager is not an EntityManagerInterface.');
        }

        $propertyName = $propertyName instanceof ClassName ? $propertyName->getShortName() : $propertyName;

        return $entityManager
            ->getConfiguration()
            ->getNamingStrategy()
            ->propertyToColumnName($propertyName);
    }

    public function isKeyword(string $name): bool
    {
        /**
         * @var Connection $connection
         */
        $connection = $this->registry->getConnection();
        $platform = $connection->getDatabasePlatform();

        return $platform->getReservedKeywordsList()->isKeyword($name);
    }

    /**
     * Determines if the property-type will make the column type redundant.
     *
     * See ClassMetadataInfo::validateAndCompleteTypedFieldMapping()
     */
    public function canColumnTypeBeInferredByPropertyType(AbstractType $type): bool
    {
        // todo: guessing on enum's could be added

        return match ($type::class) {
            ClassType::class => $this->isDateClassType($type),
            BooleanType::class,
            FloatType::class,
            IntegerType::class,
            StringType::class,
            ArrayType::class => true,
            default => false,
        };
    }

    private function isDateClassType(ClassType $type): bool
    {
        $className = $type->getClassName();

        return $className->isSame(ClassName::fromString(\DateInterval::class)) ||
            $className->isSame(ClassName::fromString(\DateTime::class)) ||
            $className->isSame(ClassName::fromString(\DateTimeImmutable::class));
    }

    public function getTypeConstant(AbstractType $type): ?string
    {
        return match ($type::class) {
            StringType::class => Types::STRING,
            ArrayType::class => Types::JSON,
            BooleanType::class => Types::BOOLEAN,
            IntegerType::class => Types::INTEGER,
            FloatType::class => Types::FLOAT,
            ObjectType::class => Types::JSON,
            ClassType::class => $this->getClassTypeConstant($type),
            default => null,
        };
    }

    private function getClassTypeConstant(ClassType $type): ?string
    {
        $className = $type->getClassName();
        $class = $className->getName();

        return match ($class) {
            \DateInterval::class => Types::DATEINTERVAL,
            \DateTimeInterface::class => Types::DATETIME_MUTABLE,
            \DateTimeImmutable::class => Types::DATETIME_IMMUTABLE,
            Ulid::class => 'ulid',
            Uuid::class => 'uuid',
            default => null,
        };
    }
}
