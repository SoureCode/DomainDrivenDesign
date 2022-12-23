<?php

declare(strict_types=1);

namespace SoureCode\DomainDrivenDesign\Integration\Symfony\Maker;

use SoureCode\DomainDrivenDesign\BoundingContext\BoundingContextArea;
use SoureCode\DomainDrivenDesign\BoundingContext\Domain\Model\Model;
use SoureCode\DomainDrivenDesign\BoundingContext\Infrastructure\Repository\RepositoryType;
use SoureCode\DomainDrivenDesign\DomainDrivenDesign;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\String\UnicodeString;

abstract class AbstractDomainDrivenDesignMaker extends AbstractMaker
{
    protected DomainDrivenDesign $domainDrivenDesign;

    public function __construct(DomainDrivenDesign $domainDrivenDesign)
    {
        $this->domainDrivenDesign = $domainDrivenDesign;
    }

    protected function getClassName(string $name): string
    {
        return (new UnicodeString(trim($name)))->camel()->title()->toString();
    }

    protected function getSlugName(string $name): string
    {
        return (new UnicodeString(trim($name)))->snake()->toString();
    }

    protected function getBoundingContextNames(): array
    {
        return array_map(static function (BoundingContextArea $boundingContext) {
            return $boundingContext->getName();
        }, $this->domainDrivenDesign->getBoundingContexts());
    }

    protected function resolveModelNames(CompletionInput $input): array
    {
        $boundingContextName = $input->getArgument('bounding-context');

        if (null === $boundingContextName) {
            return [];
        }

        return $this->getModelNames($boundingContextName);
    }

    protected function getModelNames(string $boundingContextName): array
    {
        $boundingContext = $this->domainDrivenDesign->getBoundingContext($boundingContextName);

        return array_map(static function (Model $model) {
            return $model->getName();
        }, $boundingContext->domain()->getModels());
    }

    protected function getRepositoryTypes(): array
    {
        return array_map(static function (RepositoryType $type) {
            return $type->value;
        }, RepositoryType::cases());
    }

    protected function getBoundingContextChoices(): array
    {
        return array_values($this->getBoundingContextNames());
    }

    protected function getModelChoices(string $boundingContextName): array
    {
        return array_values($this->getModelNames($boundingContextName));
    }

    protected function getRepositoryTypeChoices(): array
    {
        return array_values($this->getRepositoryTypes());
    }

    protected function createModelQuestion(InputArgument $argument, string $boundingContextName): ChoiceQuestion
    {
        $boundingContext = $this->domainDrivenDesign->getBoundingContext($boundingContextName);
        $models = $boundingContext->domain()->getModels();
        $choices = array_map(static fn (Model $model) => $model->getName(), $models);
        $choices = array_values($choices);

        return $this->createChoiceQuestion($argument, $choices);
    }

    protected function createRepositoryTypeQuestion(InputArgument $argument): ChoiceQuestion
    {
        $choices = $this->getRepositoryTypes();
        $choices = array_values($choices);

        return $this->createChoiceQuestion($argument, $choices);
    }

    protected function createChoiceQuestion(InputArgument|InputOption $input, array $choices): ChoiceQuestion
    {
        $question = new ChoiceQuestion($input->getDescription(), $choices);
        $question->setAutocompleterValues($choices);
        $question->setMaxAttempts(3);

        return $question;
    }

    protected function askOptionChoice(InputInterface $input, ConsoleStyle $io, Command $command, string $name, callable $choices)
    {
        if (null === $input->getOption($name)) {
            $option = $command->getDefinition()->getOption($name);

            $question = $this->createChoiceQuestion($option, $choices());

            $input->setOption($name, $io->askQuestion($question));
        }
    }

    protected function askArgumentChoice(InputInterface $input, ConsoleStyle $io, Command $command, string $name, callable $choices)
    {
        if (null === $input->getArgument($name)) {
            $option = $command->getDefinition()->getArgument($name);

            $question = $this->createChoiceQuestion($option, $choices());

            $input->setArgument($name, $io->askQuestion($question));
        }
    }
}
