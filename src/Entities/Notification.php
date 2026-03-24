<?php

namespace App\Entities;

use Cycle\Annotated\Annotation as Cycle;
use DateTimeInterface;

/**
 * @Cycle\Entity(table="notifications")
 * @Cycle\Table(indexes={
 *     @Cycle\Index(columns={"user_id"}),
 *     @Cycle\Index(columns={"is_read"}),
 *     @Cycle\Index(columns={"type"}),
 *     @Cycle\Index(columns={"created_at"})
 * })
 */
class Notification
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
     * @Cycle\Column(type="string", length=30, nullable=false)
     */
    private string $type;

    /**
     * @Cycle\Column(type="string", length=255, nullable=false)
     */
    private string $title;

    /**
     * @Cycle\Column(type="text", nullable=false)
     */
    private string $message;

    /**
     * @Cycle\Column(type="boolean", name="is_read", nullable=false)
     */
    private bool $isRead = false;

    /**
     * @Cycle\Column(type="json", nullable=true)
     */
    private ?array $metadata = null;

    /**
     * @Cycle\Column(type="datetime", name="created_at")
     */
    private DateTimeInterface $createdAt;

    /**
     * @Cycle\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $readAt = null;

    public function __construct(int $userId, string $type, string $title, string $message)
    {
        $this->userId = $userId;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
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

    // Setters
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