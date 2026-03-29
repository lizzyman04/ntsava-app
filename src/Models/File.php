<?php

namespace Source\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeInterface;

#[Entity(table: "files")]
#[Table(indexes: [
    new Index(columns: ["user_id"]),
    new Index(columns: ["uuid"], unique: true),
    new Index(columns: ["deleted_at"]),
    new Index(columns: ["created_at"])
])]
class File
{
    #[Column(type: "bigPrimary")]
    private int $id;

    #[Column(type: "bigInteger", name: "user_id", nullable: false)]
    private int $userId;

    #[Column(type: "string", length: 36, nullable: false)]
    private string $uuid;

    #[Column(type: "string", length: 500, nullable: true)]
    private ?string $storagePath = null;

    #[Column(type: "string", length: 255, nullable: false)]
    private string $originalName;

    #[Column(type: "bigInteger", name: "size_bytes", nullable: false)]
    private int $sizeBytes;

    #[Column(type: "string", length: 100, nullable: false)]
    private string $mimeType;

    #[Column(type: "integer", nullable: true)]
    private ?int $width = null;

    #[Column(type: "integer", nullable: true)]
    private ?int $height = null;

    #[Column(type: "integer", nullable: true)]
    private ?int $durationSeconds = null;

    #[Column(type: "datetime", name: "created_at")]
    private DateTimeInterface $createdAt;

    #[Column(type: "datetime", nullable: true)]
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

    public function getPublicUrl(): string
    {
        $baseUrl = env('CDN_URL', 'https://cdn.tudocomlizzyman.com');
        return rtrim($baseUrl, '/') . '/' . ltrim($this->storagePath, '/');
    }
}