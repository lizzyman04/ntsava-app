<?php

namespace Source\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeInterface;

#[Entity(table: "notifications")]
#[Table(indexes: [
    new Index(columns: ["user_id"]),
    new Index(columns: ["is_read"]),
    new Index(columns: ["type"]),
    new Index(columns: ["created_at"])
])]
class Notification
{
    #[Column(type: "bigPrimary")]
    private int $id;

    #[Column(type: "bigInteger", name: "user_id", nullable: false)]
    private int $userId;

    #[Column(type: "string", length: 30, nullable: false)]
    private string $type;

    #[Column(type: "string", length: 255, nullable: false)]
    private string $title;

    #[Column(type: "text", nullable: false)]
    private string $message;

    #[Column(type: "boolean", name: "is_read", nullable: false)]
    private bool $isRead = false;

    #[Column(type: "json", nullable: true)]
    private ?array $metadata = null;

    #[Column(type: "datetime", name: "created_at")]
    private DateTimeInterface $createdAt;

    #[Column(type: "datetime", nullable: true)]
    private ?DateTimeInterface $readAt = null;

    public function __construct(int $userId, string $type, string $title, string $message)
    {
        $this->userId = $userId;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
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
    public function getType(): string
    {
        return $this->type;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
    public function isRead(): bool
    {
        return $this->isRead;
    }
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
    public function getReadAt(): ?DateTimeInterface
    {
        return $this->readAt;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }
    public function markAsRead(): self
    {
        $this->isRead = true;
        $this->readAt = new \DateTime();
        return $this;
    }
    public function markAsUnread(): self
    {
        $this->isRead = false;
        $this->readAt = null;
        return $this;
    }
    public function isSystem(): bool
    {
        return $this->type === 'system';
    }
    public function isStorageWarning(): bool
    {
        return $this->type === 'storage_warning';
    }
    public function isBandwidthWarning(): bool
    {
        return $this->type === 'bandwidth_warning';
    }
    public function isPlanExpiring(): bool
    {
        return $this->type === 'plan_expiring';
    }
}