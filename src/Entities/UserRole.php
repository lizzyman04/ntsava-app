<?php

namespace App\Entities;

use Cycle\Annotated\Annotation as Cycle;
use DateTimeInterface;

/**
 * @Cycle\Entity(table="user_roles")
 * @Cycle\Table(indexes={
 *     @Cycle\Index(columns={"user_id", "role"}, unique=true)
 * })
 */
class UserRole
{
    /**
     * @Cycle\Column(type="bigPrimary")
     */
    private int $id;

    /**
     * @Cycle\Column(type="bigInteger", name="user_id", nullable=false)
     */
    private int $userId;

    /**
     * @Cycle\Column(type="string", length=20, nullable=false)
     */
    private string $role;

    /**
     * @Cycle\Column(type="datetime", name="created_at")
     */
    private DateTimeInterface $createdAt;

    public function __construct(int $userId, string $role)
    {
        $this->userId = $userId;
        $this->role = $role;
        $this->createdAt = new \DateTime();
    }

    // Getters
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

    // Setters
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