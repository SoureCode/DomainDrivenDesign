<?php

declare(strict_types=1);

use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\InfrastructureArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\InfrastructureAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\DoctrineRepository;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\InMemoryRepository;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\RepositoryArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\RepositoryAreaFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\RepositoryFactory;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\UpstreamRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    require_once __DIR__ . '/_service_name_generator.php';

    $parameters = $container->parameters();

    $parameters->set(infrastructureName('infrastructure_area_class'), InfrastructureArea::class);
    $parameters->set(infrastructureName('repository_area_class'), RepositoryArea::class);
    $parameters->set(infrastructureName('in_memory_repository_class'), InMemoryRepository::class);
    $parameters->set(infrastructureName('doctrine_repository_class'), DoctrineRepository::class);
    $parameters->set(infrastructureName('upstream_repository_class'), UpstreamRepository::class);

    $services = $container->services();

    $services->set(infrastructureName('in_memory_repository_factory'), RepositoryFactory::class)
        ->args([
            param(infrastructureName('in_memory_repository_class')),
        ]);

    $services->set(infrastructureName('doctrine_repository_factory'), RepositoryFactory::class)
        ->args([
            param(infrastructureName('doctrine_repository_class')),
        ]);

    $services->set(infrastructureName('upstream_repository_factory'), RepositoryFactory::class)
        ->args([
            param(infrastructureName('upstream_repository_class')),
        ]);

    $services->set(infrastructureName('repository_area_factory'), RepositoryAreaFactory::class)
        ->args([
            param(infrastructureName('repository_area_class')),
            service(infrastructureName('in_memory_repository_factory')),
            service(infrastructureName('doctrine_repository_factory')),
            service(infrastructureName('upstream_repository_factory')),
        ]);

    $services->set(infrastructureName('infrastructure_area_factory'), InfrastructureAreaFactory::class)
        ->args([
            param(infrastructureName('infrastructure_area_class')),
            service(infrastructureName('repository_area_factory')),
        ]);
};
