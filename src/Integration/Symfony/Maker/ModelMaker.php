<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Symfony\Maker;

use SoureCode\DomainDrivenDesign\DomainDrivenDesign;
use SoureCode\DomainDrivenDesign\Integration\Maker\Writer\MakerBundleWriter;
use SoureCode\PhpObjectModel\ValueObject\ClassName;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

class ModelMaker extends AbstractDomainDrivenDesignMaker
{
    private ClassName $aggregateRootIdClass;

    public function __construct(DomainDrivenDesign $domainDrivenDesign, string $aggregateRootIdClass)
    {
        parent::__construct($domainDrivenDesign);

        $this->aggregateRootIdClass = ClassName::fromString($aggregateRootIdClass);
    }

    public static function getCommandName(): string
    {
        return 'make:ddd:model';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new DDD model';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument(
                'bounding-context',
                InputArgument::REQUIRED,
                'The name of the bounding context (e.g. <fg=yellow>BookStore</>)',
                null,
                $this->getBoundingContextNames(...),
            )
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the model to create (e.g. <fg=yellow>Book</>)');

        $inputConfig->setArgumentAsNonInteractive('bounding-context');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        $this->askArgumentChoice($input, $io, $command, 'bounding-context', $this->getBoundingContextChoices(...));
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $modelName = $input->getArgument('name');
        $boundingContextName = $input->getArgument('bounding-context');
        $boundingContext = $this->domainDrivenDesign->getBoundingContext($boundingContextName);

        $domain = $boundingContext->domain();
        $model = $domain->createModel($modelName);
        $modelId = $domain->createValueObject($modelName . 'Id');
        $modelIdClass = $modelId->getFile()->getClass();

        if ($modelIdClass->hasMethod('__construct')) {
            $modelIdClass->removeMethod('__construct');
        }

        if ($modelIdClass->hasProperty('value')) {
            $modelIdClass->removeProperty('value');
        }

        $modelIdClass->extend($this->aggregateRootIdClass);

        $model->addProperty($modelId, 'id', false);

        $this->domainDrivenDesign->write(new MakerBundleWriter($generator));

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text([
            'Next: create some value objects!',
        ]);
    }
}
