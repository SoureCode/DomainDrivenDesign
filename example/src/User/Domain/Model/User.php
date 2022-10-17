<?php

declare(strict_types=1);

namespace App\User\Domain\Model;

use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\UserId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class User
{
    #[ORM\Embedded(columnPrefix: false)]
    public UserId $id;
    #[ORM\Embedded(columnPrefix: false)]
    public Email $email;

    public function __construct(Email $email)
    {
        $this->id = new UserId();
        $this->email = $email;
    }
}
