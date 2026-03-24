<?php

namespace App\Entities;

use Cycle\Annotated\Annotation as Cycle;
use DateTimeInterface;

/**
 * @Cycle\Entity(table="api_tokens")
 * @Cycle\Table(indexes={
 *     @Cycle\Index(columns={"token_hash"}, unique=true),
 *     @Cycle\Index(columns={"user_id"}),
 *     @Cycle\Index(columns={"expires_at"})
 * })
 */
class ApiToken
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
     * @Cycle\Column(type="string", length=255, nullable=false)
     */
    private string $tokenHash;

    /**
     * @Cycle\Column(type="string", length=100, nullable=false)
     */
    private string $name;

    /**
     * @Cycle\Column(type="json", nullable=false)
     */
    private array $permissions = ['upload', 'delete', 'read'];

    /**
     * @Cycle\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $lastUsedAt = null;

    /**
     * @Cycle\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $expiresAt = null;

    /**
     * @Cycle\Column(type="datetime", name="created_at")
     */
    private DateTimeInterface $createdAt;

    public function __construct(int $userId, string $name, string $tokenHash)
    {
        $this->userId = $userId;
        $this->name = $name;
        $this->tokenHash = $tokenHash;
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
    public function getTokenHash(): string
    {
        return $this->tokenHash;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getPermissions(): array
    {
        return $this->permissions;
    }
    public function getLastUsedAt(): ?DateTimeInterface
    {
        return $this->lastUsedAt;
    }
    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt;
    }
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    // Setters
    public function setPermissions(array $permissions): self
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function setLastUsedAt(?DateTimeInterface $time): self
    {
        $this->lastUsedAt = $time;
        return $this;
    }

    public function setExpiresAt(?DateTimeInterface $time): self
    {
        $this->expiresAt = $time;
        return $this;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions);
    }

    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }
        return $this->expiresAt < new \DateTime();
    }

    public function touch(): self
    {
        $this->lastUsedAt = new \DateTime();
        return $this;
    }
}