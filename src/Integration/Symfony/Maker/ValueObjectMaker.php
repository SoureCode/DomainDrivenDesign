<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Symfony\Maker;

use SoureCode\DomainDrivenDesign\BoundingContext\Domain\ValueObject\ValueObjectInterface;
use SoureCode\DomainDrivenDesign\Integration\Maker\Writer\MakerBundleWriter;
use SoureCode\PhpObjectModel\Type\ArrayType;
use SoureCode\PhpObjectModel\Type\BooleanType;
use SoureCode\PhpObjectModel\Type\ClassType;
use SoureCode\PhpObjectModel\Type\FloatType;
use SoureCode\PhpObjectModel\Type\IntegerType;
use SoureCode\PhpObjectModel\Type\StringType;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class ValueObjectMaker extends AbstractDomainDrivenDesignMaker
{
    public static function getCommandName(): string
    {
        return 'make:ddd:value-object';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new DDD value object';
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
            ->addArgument('value-object', InputArgument::REQUIRED, 'The name of the value object to create (e.g. <fg=yellow>BookTitle</>)')
            ->addOption('init', null, InputOption::VALUE_REQUIRED, 'How to initialize the value object (pass/construct/passOrConstruct)', null, [
                'pass',
                'construct',
                'passOrConstruct',
            ])
            ->addOption('type', null, InputOption::VALUE_REQUIRED, 'The type of the value object (string/int/float/bool/array)', null, [
                'string',
                'int',
                'float',
                'bool',
                'array',
            ])
            ->addOption('class', null, InputOption::VALUE_REQUIRED, 'The class type of the value object (e.g. <fg=yellow>DateTime</>)');

        $inputConfig->setArgumentAsNonInteractive('bounding-context');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        $this->askArgumentChoice($input, $io, $command, 'bounding-context', $this->getBoundingContextChoices(...));

        if (!$input->hasOption('class')) {
            $this->askOptionChoice($input, $io, $command, 'type', function () {
                return [
                    'string',
                    'int',
                    'float',
                    'bool',
                    'array',
                ];
            });
        }
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $boundingContextName = $input->getArgument('bounding-context');
        $valueObjectName = $input->getArgument('value-object');
        $initOption = $input->getOption('init');
        $typeOption = $input->getOption('type');
        $classOption = $input->getOption('class');
        $boundingContext = $this->domainDrivenDesign->getBoundingContext($boundingContextName);

        $domain = $boundingContext->domain();
        $domain->createValueObject($valueObjectName);

        $valueObject = $domain->createValueObject($valueObjectName);

        if (null !== $typeOption && null !== $classOption) {
            throw new \InvalidArgumentException('You can\'t use type and class option at the same time.');
        }

        $currentType = $valueObject->getType();
        $wasClassType = $currentType instanceof ClassType;

        if ($wasClassType) {
            $this->setInit($initOption, $valueObject);
        }

        $this->setType($typeOption, $valueObject);
        $this->setClassType($classOption, $valueObject);

        if (!$wasClassType) {
            $this->setInit($initOption, $valueObject);
        }

        $this->domainDrivenDesign->write(new MakerBundleWriter($generator));

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
        $io->text([
            'Next: add the value object to the model!',
        ]);
    }

    protected function setInit(mixed $initOption, ValueObjectInterface $valueObject): void
    {
        if (null !== $initOption) {
            if ('pass' === $initOption) {
                $valueObject->setPass();
            } elseif ('construct' === $initOption) {
                $valueObject->setConstruct();
            } elseif ('passOrConstruct' === $initOption) {
                $valueObject->setPassOrConstruct();
            }
        }
    }

    protected function setType(mixed $typeOption, ValueObjectInterface $valueObject): void
    {
        if (null !== $typeOption) {
            if ('string' === $typeOption) {
                $valueObject->setType(new StringType());
            } elseif ('int' === $typeOption) {
                $valueObject->setType(new IntegerType());
            } elseif ('float' === $typeOption) {
                $valueObject->setType(new FloatType());
            } elseif ('bool' === $typeOption) {
                $valueObject->setType(new BooleanType());
            } elseif ('array' === $typeOption) {
                $valueObject->setType(new ArrayType());
            } else {
                throw new \InvalidArgumentException(sprintf('The type "%s" is not supported.', $typeOption));
            }
        }
    }

    protected function setClassType(mixed $classOption, ValueObjectInterface $valueObject): void
    {
        if (null !== $classOption) {
            $valueObject->setType(new ClassType($classOption));
        }
    }
}
