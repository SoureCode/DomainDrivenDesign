<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Doctrine;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;
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

    public function getPotentialTableName(ClassName|string $className): string
    {
        $entityManager = $this->registry->getManager();

        if (!$entityManager instanceof EntityManagerInterface) {
            throw new RuntimeException('ObjectManager is not an EntityManagerInterface.');
        }

        $className = $className instanceof ClassName ? $className->getShortName() : $className;

        return $entityManager
            ->getConfiguration()
            ->getNamingStrategy()
            ->classToTableName(strtolower($className));
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

        return $className->isSame(ClassName::fromString(DateInterval::class)) ||
            $className->isSame(ClassName::fromString(DateTime::class)) ||
            $className->isSame(ClassName::fromString(DateTimeImmutable::class));
    }

    public function getPropertyTypeForColumn(string $columnType): ?AbstractType
    {
        return match ($columnType) {
            Types::STRING, Types::TEXT, Types::GUID, Types::BIGINT, Types::DECIMAL => new StringType(),
            Types::ARRAY, Types::SIMPLE_ARRAY, Types::JSON => new ArrayType(),
            Types::BOOLEAN => new BooleanType(),
            Types::INTEGER, Types::SMALLINT => new IntegerType(),
            Types::FLOAT => new FloatType(),
            Types::DATETIME_MUTABLE, Types::DATETIMETZ_MUTABLE,
            Types::DATE_MUTABLE, Types::TIME_MUTABLE => new ClassType(DateTimeInterface::class),
            Types::DATETIME_IMMUTABLE, Types::DATETIMETZ_IMMUTABLE,
            Types::DATE_IMMUTABLE, Types::TIME_IMMUTABLE => new ClassType(DateTimeImmutable::class),
            Types::DATEINTERVAL => new ClassType(DateInterval::class),
            Types::OBJECT => new ObjectType(),
            'uuid' => new ClassType(Uuid::class),
            'ulid' => new ClassType(Ulid::class),
            default => null,
        };
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
            DateInterval::class => Types::DATEINTERVAL,
            DateTimeInterface::class => Types::DATETIME_MUTABLE,
            DateTimeImmutable::class => Types::DATETIME_IMMUTABLE,
            Ulid::class => 'ulid',
            Uuid::class => 'uuid',
            default => null,
        };
    }
}
