<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class Email
{
    #[ORM\Column(name: 'email')]
    public readonly string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
