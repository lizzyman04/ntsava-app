<?php

namespace App\Entities;

use Cycle\Annotated\Annotation as Cycle;
use DateTimeInterface;

/**
 * @Cycle\Entity(table="files")
 * @Cycle\Table(indexes={
 *     @Cycle\Index(columns={"user_id"}),
 *     @Cycle\Index(columns={"uuid"}, unique=true),
 *     @Cycle\Index(columns={"deleted_at"}),
 *     @Cycle\Index(columns={"created_at"})
 * })
 */
class File
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
     * @Cycle\Column(type="string", length=36, nullable=false)
     */
    private string $uuid;

    /**
     * @Cycle\Column(type="string", length=500, nullable=true)
     */
    private ?string $storagePath = null;

    /**
     * @Cycle\Column(type="string", length=255, nullable=false)
     */
    private string $originalName;

    /**
     * @Cycle\Column(type="bigInteger", name="size_bytes", nullable=false)
     */
    private int $sizeBytes;

    /**
     * @Cycle\Column(type="string", length=100, nullable=false)
     */
    private string $mimeType;

    /**
     * @Cycle\Column(type="integer", nullable=true)
     */
    private ?int $width = null;

    /**
     * @Cycle\Column(type="integer", nullable=true)
     */
    private ?int $height = null;

    /**
     * @Cycle\Column(type="integer", nullable=true)
     */
    private ?int $durationSeconds = null;

    /**
     * @Cycle\Column(type="datetime", name="created_at")
     */
    private DateTimeInterface $createdAt;

    /**
     * @Cycle\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $deletedAt = null;

    public function __construct(int $userId, string $originalName, int $sizeBytes, string $mimeType)
    {
        $this->userId = $userId;
        $this->originalName = $originalName;
        $this->sizeBytes = $sizeBytes;
        $this->mimeType = $mimeType;
        $this->uuid = $this->generateUuid();
        $this->createdAt = new \DateTime();
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
    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getUuid(): string
    {
        return $this->uuid;
    }
    public function getStoragePath(): ?string
    {
        return $this->storagePath;
    }
    public function getOriginalName(): string
    {
        return $this->originalName;
    }
    public function getSizeBytes(): int
    {
        return $this->sizeBytes;
    }
    public function getMimeType(): string
    {
        return $this->mimeType;
    }
    public function getWidth(): ?int
    {
        return $this->width;
    }
    public function getHeight(): ?int
    {
        return $this->height;
    }
    public function getDurationSeconds(): ?int
    {
        return $this->durationSeconds;
    }
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deletedAt;
    }

    // Setters
    public function setStoragePath(?string $path): self
    {
        $this->storagePath = $path;
        return $this;
    }

    public function setDimensions(?int $width, ?int $height): self
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    public function setDurationSeconds(?int $seconds): self
    {
        $this->durationSeconds = $seconds;
        return $this;
    }

    public function delete(): self
    {
        $this->deletedAt = new \DateTime();
        $this->storagePath = null;
        return $this;
    }

    public function restore(): self
    {
        $this->deletedAt = null;
        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mimeType, 'image/');
    }

    public function isVideo(): bool
    {
        return str_starts_with($this->mimeType, 'video/');
    }

    public function getExtension(): string
    {
        return pathinfo($this->originalName, PATHINFO_EXTENSION);
    }

    public function getFilename(): string
    {
        return pathinfo($this->originalName, PATHINFO_FILENAME);
    }
}