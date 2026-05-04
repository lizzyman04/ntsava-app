<?php

namespace Source\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use DateTime;
use DateTimeInterface;

#[Entity(table: "plans")]
#[Table(indexes: [
    new Index(columns: ["slug"], unique: true),
    new Index(columns: ["is_active"]),
    new Index(columns: ["currency"])
])]
class Plan
{
    #[Column(type: "primary")]
    private int $id;

    #[Column(type: "string", length: 50, nullable: false)]
    private string $name;

    #[Column(type: "string", length: 50, nullable: false)]
    private string $slug;

    #[Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[Column(type: "bigInteger", name: "storage_limit_bytes", nullable: false)]
    private int $storageLimitBytes;

    #[Column(type: "bigInteger", name: "bandwidth_limit_bytes", nullable: false)]
    private int $bandwidthLimitBytes;

    #[Column(type: "bigInteger", name: "max_file_size_bytes", nullable: false)]
    private int $maxFileSizeBytes = 10485760;

    #[Column(type: "text", name: "allowed_mime_types", nullable: false)]
    private string $allowedMimeTypes = '["jpg","jpeg","png","gif","webp"]';

    #[Column(type: "decimal", precision: 10, scale: 2, nullable: false)]
    private float $price = 0.00;

    #[Column(type: "string", length: 3, nullable: false)]
    private string $currency = 'MZN';

    #[Column(type: "boolean", name: "is_active", nullable: false)]
    private bool $isActive = true;

    #[Column(type: "integer", name: "sort_order", nullable: false)]
    private int $sortOrder = 0;

    #[Column(type: "datetime", name: "created_at")]
    private DateTimeInterface $createdAt;

    #[Column(type: "datetime", name: "updated_at")]
    private DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getSlug(): string
    {
        return $this->slug;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getStorageLimitBytes(): int
    {
        return $this->storageLimitBytes;
    }
    public function getBandwidthLimitBytes(): int
    {
        return $this->bandwidthLimitBytes;
    }
    public function getMaxFileSizeBytes(): int
    {
        return $this->maxFileSizeBytes;
    }
    public function getAllowedMimeTypes(): array
    {
        return json_decode($this->allowedMimeTypes, true) ?? ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    }
    public function getPrice(): float
    {
        return $this->price;
    }
    public function getCurrency(): string
    {
        return $this->currency;
    }
    public function isActive(): bool
    {
        return $this->isActive;
    }
    public function getSortOrder(): int
    {
        return $this->sortOrder;
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
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }
    public function setStorageLimitBytes(int $bytes): self
    {
        $this->storageLimitBytes = $bytes;
        return $this;
    }
    public function setBandwidthLimitBytes(int $bytes): self
    {
        $this->bandwidthLimitBytes = $bytes;
        return $this;
    }
    public function setMaxFileSizeBytes(int $bytes): self
    {
        $this->maxFileSizeBytes = $bytes;
        return $this;
    }
    public function setAllowedMimeTypes(array $types): self
    {
        $this->allowedMimeTypes = json_encode($types);
        return $this;
    }
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }
    public function setSortOrder(int $order): self
    {
        $this->sortOrder = $order;
        return $this;
    }
    public function updateTimestamps(): self
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    public function isFree(): bool
    {
        return $this->price == 0;
    }
    public function isBusiness(): bool
    {
        return $this->slug === 'business';
    }
    public function hasUnlimitedStorage(): bool
    {
        return $this->storageLimitBytes === 0;
    }
    public function hasUnlimitedBandwidth(): bool
    {
        return $this->bandwidthLimitBytes === 0;
    }
    public function getFormattedPrice(): string
    {
        if ($this->price == 0)
            return 'Grátis';
        return $this->currency . ' ' . number_format($this->price, 2);
    }
}