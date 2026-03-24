<?php

namespace Source\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use DateTime;
use DateTimeInterface;

#[Entity(table: "users")]
#[Table(indexes: [
    new Index(columns: ["email"], unique: true),
    new Index(columns: ["username"], unique: true),
    new Index(columns: ["uuid"], unique: true),
    new Index(columns: ["status"])
])]
class User
{
    #[Column(type: "bigPrimary")]
    private int $id;

    #[Column(type: "string", length: 36, nullable: false)]
    private string $uuid;

    #[Column(type: "string", length: 100, nullable: false)]
    private string $name;

    #[Column(type: "string", length: 50, nullable: false)]
    private string $username;

    #[Column(type: "string", length: 255, nullable: false)]
    private string $email;

    #[Column(type: "string", length: 255, nullable: false)]
    private string $password_hash;

    #[Column(type: "integer", name: "plan_id", nullable: false)]
    private int $planId;

    #[Column(type: "bigInteger", name: "storage_limit_bytes", nullable: false)]
    private int $storageLimitBytes;

    #[Column(type: "bigInteger", name: "storage_used_bytes", nullable: false)]
    private int $storageUsedBytes = 0;

    #[Column(type: "bigInteger", name: "bandwidth_limit_bytes", nullable: false)]
    private int $bandwidthLimitBytes;

    #[Column(type: "bigInteger", name: "bandwidth_used_bytes", nullable: false)]
    private int $bandwidthUsedBytes = 0;

    #[Column(type: "integer", name: "bandwidth_reset_month", nullable: false)]
    private int $bandwidthResetMonth;

    #[Column(type: "string", length: 20, nullable: false)]
    private string $status = 'active';

    #[Column(type: "text", nullable: true)]
    private ?string $suspendedReason = null;

    #[Column(type: "datetime", nullable: true)]
    private ?DateTimeInterface $suspendedAt = null;

    #[Column(type: "datetime", nullable: true)]
    private ?DateTimeInterface $lastLoginAt = null;

    #[Column(type: "datetime", name: "created_at")]
    private DateTimeInterface $createdAt;

    #[Column(type: "datetime", name: "updated_at")]
    private DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->uuid = $this->generateUuid();
        $this->bandwidthResetMonth = (int) date('Ym');
    }

    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }
    public function getUuid(): string
    {
        return $this->uuid;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }
    public function getPlanId(): int
    {
        return $this->planId;
    }
    public function getStorageLimitBytes(): int
    {
        return $this->storageLimitBytes;
    }
    public function getStorageUsedBytes(): int
    {
        return $this->storageUsedBytes;
    }
    public function getBandwidthLimitBytes(): int
    {
        return $this->bandwidthLimitBytes;
    }
    public function getBandwidthUsedBytes(): int
    {
        return $this->bandwidthUsedBytes;
    }
    public function getBandwidthResetMonth(): int
    {
        return $this->bandwidthResetMonth;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getSuspendedReason(): ?string
    {
        return $this->suspendedReason;
    }
    public function getSuspendedAt(): ?DateTimeInterface
    {
        return $this->suspendedAt;
    }
    public function getLastLoginAt(): ?DateTimeInterface
    {
        return $this->lastLoginAt;
    }
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    // Setters
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function setPasswordHash(string $hash): self
    {
        $this->password_hash = $hash;
        return $this;
    }
    public function setPlanId(int $planId): self
    {
        $this->planId = $planId;
        return $this;
    }
    public function setStorageLimitBytes(int $limit): self
    {
        $this->storageLimitBytes = $limit;
        return $this;
    }
    public function addStorageUsedBytes(int $bytes): self
    {
        $this->storageUsedBytes += $bytes;
        return $this;
    }
    public function subtractStorageUsedBytes(int $bytes): self
    {
        $this->storageUsedBytes -= $bytes;
        return $this;
    }
    public function setBandwidthLimitBytes(int $limit): self
    {
        $this->bandwidthLimitBytes = $limit;
        return $this;
    }
    public function addBandwidthUsedBytes(int $bytes): self
    {
        $this->bandwidthUsedBytes += $bytes;
        return $this;
    }
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
    public function suspend(?string $reason = null): self
    {
        $this->status = 'suspended';
        $this->suspendedReason = $reason;
        $this->suspendedAt = new DateTime();
        return $this;
    }
    public function activate(): self
    {
        $this->status = 'active';
        $this->suspendedReason = null;
        $this->suspendedAt = null;
        return $this;
    }
    public function setLastLoginAt(?DateTimeInterface $time): self
    {
        $this->lastLoginAt = $time;
        return $this;
    }
    public function updateTimestamps(): self
    {
        $this->updatedAt = new DateTime();
        return $this;
    }
    public function resetBandwidthIfNeeded(): self
    {
        $currentMonth = (int) date('Ym');
        if ($this->bandwidthResetMonth !== $currentMonth) {
            $this->bandwidthUsedBytes = 0;
            $this->bandwidthResetMonth = $currentMonth;
        }
        return $this;
    }
    public function hasStorageAvailable(int $bytes): bool
    {
        return ($this->storageUsedBytes + $bytes) <= $this->storageLimitBytes;
    }
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
    public function getStorageUsagePercent(): float
    {
        if ($this->storageLimitBytes == 0)
            return 0;
        return ($this->storageUsedBytes / $this->storageLimitBytes) * 100;
    }
    public function getBandwidthUsagePercent(): float
    {
        if ($this->bandwidthLimitBytes == 0)
            return 0;
        return ($this->bandwidthUsedBytes / $this->bandwidthLimitBytes) * 100;
    }
    public function getStoragePath(): string
    {
        return 'u/' . $this->username;
    }
    public function getPublicUrl(string $filePath): string
    {
        return 'https://cdn.tudocomlizzyman.com/' . $this->getStoragePath() . '/' . ltrim($filePath, '/');
    }
}