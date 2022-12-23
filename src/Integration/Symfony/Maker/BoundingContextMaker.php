<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Symfony\Maker;

use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextAreaInterface;
use SoureCode\DomainDrivenDesign\DomainDrivenDesign;
use SoureCode\DomainDrivenDesign\Integration\Maker\Writer\MakerBundleWriter;
use SoureCode\DomainDrivenDesign\Integration\Symfony\Maker\Factory\ServiceFileFactory;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Path;

class BoundingContextMaker extends AbstractDomainDrivenDesignMaker
{
    private ServiceFileFactory $serviceFileFactory;

    public function __construct(DomainDrivenDesign $domainDrivenDesign, ServiceFileFactory $serviceFileFactory)
    {
        parent::__construct($domainDrivenDesign);
        $this->serviceFileFactory = $serviceFileFactory;
    }

    public static function getCommandName(): string
    {
        return 'make:ddd:bounding-context';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new DDD context';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the context to create (e.g. <fg=yellow>Customer</>)');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $contextNameInput = $input->getArgument('name');
        $boundingContext = $this->domainDrivenDesign->createBoundingContext($contextNameInput);

        if (file_exists($boundingContext->getDirectory())) {
            $io->error(sprintf('The bounding context "%s" already exists.', $boundingContext->getName()));

            return;
        }

        $generator->dumpFile(Path::join($boundingContext->getDirectory(), '.gitignore'), '');

        $this
            ->generateServiceFile($generator, $boundingContext)
            ->generateTestServiceFile($generator, $boundingContext);

        $this->domainDrivenDesign->write(new MakerBundleWriter($generator));

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text([
            'Next: use other ddd commands to create domain models or value objects!',
        ]);
    }

    protected function generateServiceFile(Generator $generator, BoundingContextAreaInterface $boundingContext): BoundingContextMaker
    {
        $serviceFile = $this->serviceFileFactory->create($boundingContext->getNamespace(), $boundingContext->getDirectory());

        // @todo Make path configurable
        $generator->dumpFile(
            Path::join(
                'config/services/',
                $this->getSlugName($boundingContext->getName()).'.php'
            ),
            $serviceFile->getSourceCode()
        );

        return $this;
    }

    private function generateTestServiceFile(Generator $generator, BoundingContextAreaInterface $boundingContext): BoundingContextMaker
    {
        $serviceFile = $this->serviceFileFactory->create($boundingContext->getNamespace(), $boundingContext->getDirectory(), false);

        // @todo Make path configurable
        $generator->dumpFile(
            Path::join(
                'config/services/test/',
                $this->getSlugName($boundingContext->getName()).'.php'
            ),
            $serviceFile->getSourceCode()
        );

        return $this;
    }
}
