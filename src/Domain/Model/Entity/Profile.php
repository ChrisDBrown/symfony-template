<?php

declare(strict_types=1);

namespace App\Domain\Model\Entity;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

class Profile
{
    private readonly \DateTimeImmutable $createdAt;

    private \DateTimeImmutable $updatedAt;

    public function __construct(
        private readonly UuidV4 $id,
        private string $name
    ) {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function create(string $name): self
    {
        return new self(Uuid::v4(), $name);
    }

    public function update(string $name): void
    {
        $this->name = $name;
        // @TODO: updatedAt updates are handled by gedmo:timestampeable but Rector makes
        //        the property readonly if we don't update it somewhere in the class code - ReadOnlyPropertyRector
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): UuidV4
    {
        return $this->id;
    }
}
