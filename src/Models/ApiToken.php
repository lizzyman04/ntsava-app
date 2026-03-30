<?php

namespace Source\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeInterface;
use JsonSerializable;

#[Entity(table: "api_tokens")]
#[Table(indexes: [
    new Index(columns: ["token_hash"], unique: true),
    new Index(columns: ["user_id"]),
    new Index(columns: ["expires_at"])
])]
class ApiToken implements JsonSerializable
{
    #[Column(type: "bigPrimary")]
    private int $id;

    #[Column(type: "bigInteger", name: "user_id", nullable: false)]
    private int $userId;

    #[Column(type: "string", length: 255, nullable: false)]
    private string $tokenHash;

    #[Column(type: "string", length: 100, nullable: false)]
    private string $name;

    /**
     * @var array|string
     */
    #[Column(type: "json", nullable: false)]
    private $permissions;

    #[Column(type: "datetime", nullable: true)]
    private ?DateTimeInterface $lastUsedAt = null;

    #[Column(type: "datetime", nullable: true)]
    private ?DateTimeInterface $expiresAt = null;

    #[Column(type: "datetime", name: "created_at")]
    private DateTimeInterface $createdAt;

    public function __construct(int $userId, string $name, string $tokenHash)
    {
        $this->userId = $userId;
        $this->name = $name;
        $this->tokenHash = $tokenHash;
        $this->createdAt = new \DateTime();
        $this->permissions = ['upload', 'delete', 'read'];
    }

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
    
    /**
     * Get permissions as array
     * 
     * @return array
     */
    public function getPermissions(): array
    {
        $permissions = $this->permissions;
        
        if (is_string($permissions)) {
            $decoded = json_decode($permissions, true);
            return is_array($decoded) ? $decoded : ['upload', 'delete', 'read'];
        }
        
        if (is_array($permissions)) {
            return $permissions;
        }
        
        return ['upload', 'delete', 'read'];
    }
    
    /**
     * Set permissions from array
     * 
     * @param array $permissions
     * @return self
     */
    public function setPermissions(array $permissions): self
    {
        $this->permissions = $permissions;
        return $this;
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
        $perms = $this->getPermissions();
        return in_array($permission, $perms);
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

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => $this->getPermissions(),
            'last_used_at' => $this->lastUsedAt?->format('Y-m-d H:i:s'),
            'expires_at' => $this->expiresAt?->format('Y-m-d H:i:s'),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }
}