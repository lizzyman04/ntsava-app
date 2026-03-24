<?php

namespace App\Entities;

use Cycle\Annotated\Annotation as Cycle;
use DateTimeInterface;

/**
 * @Cycle\Entity(table="credit_transactions")
 * @Cycle\Table(indexes={
 *     @Cycle\Index(columns={"user_id"}),
 *     @Cycle\Index(columns={"type"}),
 *     @Cycle\Index(columns={"created_at"}),
 *     @Cycle\Index(columns={"currency"})
 * })
 */
class CreditTransaction
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
     * @Cycle\Column(type="decimal", precision=10, scale=2, nullable=false)
     */
    private float $amount;

    /**
     * @Cycle\Column(type="string", length=3, nullable=false)
     */
    private string $currency = 'MZN';

    /**
     * @Cycle\Column(type="decimal", precision=10, scale=2, name="balance_after", nullable=false)
     */
    private float $balanceAfter;

    /**
     * @Cycle\Column(type="string", length=255, nullable=true)
     */
    private ?string $description = null;

    /**
     * @Cycle\Column(type="string", length=100, nullable=true)
     */
    private ?string $referenceId = null;

    /**
     * @Cycle\Column(type="json", nullable=true)
     */
    private ?array $metadata = null;

    /**
     * @Cycle\Column(type="datetime", name="created_at")
     */
    private DateTimeInterface $createdAt;

    public function __construct(
        int $userId,
        string $type,
        float $amount,
        float $balanceAfter,
        string $currency = 'MZN'
    ) {
        $this->userId = $userId;
        $this->type = $type;
        $this->amount = $amount;
        $this->balanceAfter = $balanceAfter;
        $this->currency = $currency;
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
    public function getAmount(): float
    {
        return $this->amount;
    }
    public function getCurrency(): string
    {
        return $this->currency;
    }
    public function getBalanceAfter(): float
    {
        return $this->balanceAfter;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getReferenceId(): ?string
    {
        return $this->referenceId;
    }
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    // Setters
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setReferenceId(?string $referenceId): self
    {
        $this->referenceId = $referenceId;
        return $this;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function isPurchase(): bool
    {
        return $this->type === 'purchase';
    }

    public function isUpgrade(): bool
    {
        return $this->type === 'upgrade_plan';
    }

    public function isDowngrade(): bool
    {
        return $this->type === 'downgrade_plan';
    }
}