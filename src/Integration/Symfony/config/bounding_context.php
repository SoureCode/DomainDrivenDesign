<?php

declare(strict_types=1);

use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextArea;
use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextAreaFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    require_once __DIR__ . '/_service_name_generator.php';

    $parameters = $container->parameters();

    $parameters->set(boundingContextName('bounding_context_area_class'), BoundingContextArea::class);

    $services = $container->services();

    $services->set(boundingContextName('bounding_context_area_factory'), BoundingContextAreaFactory::class)
        ->args([
            param(boundingContextName('bounding_context_area_class')),
            service(domainName('domain_area_factory')),
            service(infrastructureName('infrastructure_area_factory')),
        ]);
};
