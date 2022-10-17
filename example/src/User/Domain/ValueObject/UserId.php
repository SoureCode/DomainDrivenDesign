<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

#[ORM\Embeddable]
final class UserId
{
    #[ORM\Column(name: 'id', type: 'ulid')]
    public readonly Ulid $value;

    public function __construct()
    {
        $this->value = new Ulid();
    }
}
