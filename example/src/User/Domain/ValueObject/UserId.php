<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

#[ORM\Embeddable]
final class UserId
{
    #[ORM\Column(name: 'userid', type: Types::ulid)]
    public readonly Ulid $value;

    public function __construct(Ulid $value)
    {
        $this->value = $value;
    }
}
