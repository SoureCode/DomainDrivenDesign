<?php

declare(strict_types=1);

use SoureCode\DomainDrivenDesign\BoundingContext\Domain\DomainArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\DomainAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\Model;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\ModelFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterface;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Repository\RepositoryInterfaceFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectFactory;
use SoureCode\DomainDrivenDesign\Integration\Doctrine\ValueObject\DoctrineValueObject;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    require_once __DIR__ . '/_service_name_generator.php';

    $parameters = $container->parameters();

    $parameters->set(domainName('domain_area_class'), DomainArea::class);
    $parameters->set(domainName('value_object_class'), DoctrineValueObject::class);
    $parameters->set(domainName('value_object_area_class'), ValueObjectArea::class);
    $parameters->set(domainName('model_class'), Model::class);
    $parameters->set(domainName('model_area_class'), ModelArea::class);
    $parameters->set(domainName('repository_area_class'), RepositoryInterfaceArea::class);
    $parameters->set(domainName('repository_class'), RepositoryInterface::class);

    $services = $container->services();

    $services->set(domainName('value_object_factory'), ValueObjectFactory::class)
        ->args([
            param(domainName('value_object_class')),
        ]);

    $services->set(domainName('value_object_area_factory'), ValueObjectAreaFactory::class)
        ->args([
            param(domainName('value_object_area_class')),
            service(domainName('value_object_factory')),
        ]);

    $services->set(domainName('model_factory'), ModelFactory::class)
        ->args([
            param(domainName('model_class')),
        ]);

    $services->set(domainName('model_area_factory'), ModelAreaFactory::class)
        ->args([
            param(domainName('model_area_class')),
            service(domainName('model_factory')),
        ]);

    $services->set(domainName('repository_factory'), RepositoryInterfaceFactory::class)
        ->args([
            param(domainName('repository_class')),
        ]);

    $services->set(domainName('repository_area_factory'), RepositoryInterfaceAreaFactory::class)
        ->args([
            param(domainName('repository_area_class')),
            service(domainName('repository_factory')),
        ]);

    $services->set(domainName('domain_area_factory'), DomainAreaFactory::class)
        ->args([
            param(domainName('domain_area_class')),
            service(domainName('model_area_factory')),
            service(domainName('value_object_area_factory')),
            service(domainName('repository_area_factory')),
        ]);
};
