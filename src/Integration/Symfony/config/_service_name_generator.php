<?php

declare(strict_types=1);

/**
 * @return string[]
 */
function prefixes(): array
{
    return ['soure_code', 'domain_driven_design'];
}

/**
 * @param string[] $prefixes
 */
function serviceName(array $prefixes, string $serviceName): string
{
    return implode('.', [...$prefixes, $serviceName]);
}

function name(string $serviceName): string
{
    return serviceName(prefixes(), $serviceName);
}

function doctrineName(string $serviceName): string
{
    return serviceName([...prefixes(), 'doctrine'], $serviceName);
}

function domainName(string $serviceName): string
{
    return serviceName([...prefixes(), 'domain'], $serviceName);
}

function infrastructureName(string $serviceName): string
{
    return serviceName([...prefixes(), 'infrastructure'], $serviceName);
}

function boundingContextName(string $serviceName): string
{
    return serviceName([...prefixes(), 'bounding_context'], $serviceName);
}

function integrationName(string $serviceName): string
{
    return serviceName([...prefixes(), 'integration'], $serviceName);
}
