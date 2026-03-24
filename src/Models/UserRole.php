<?php

namespace Source\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeInterface;

#[Entity(table: "user_roles")]
#[Table(indexes: [
    new Index(columns: ["user_id", "role"], unique: true)
])]
class UserRole
{
    #[Column(type: "bigPrimary")]
    private int $id;

    #[Column(type: "bigInteger", name: "user_id", nullable: false)]
    private int $userId;

    #[Column(type: "string", length: 20, nullable: false)]
    private string $role;

    #[Column(type: "datetime", name: "created_at")]
    private DateTimeInterface $createdAt;

    public function __construct(int $userId, string $role)
    {
        $this->userId = $userId;
        $this->role = $role;
        $this->createdAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getRole(): string
    {
        return $this->role;
    }
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }
    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}