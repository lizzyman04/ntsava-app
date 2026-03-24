<?php

namespace Source\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;
use DateTimeInterface;

#[Entity(table: "credits")]
#[Table(indexes: [
    new Index(columns: ["user_id"]),
    new Index(columns: ["currency"])
])]
class Credit
{
    #[Column(type: "bigPrimary")]
    private int $id;

    #[Column(type: "bigInteger", name: "user_id", nullable: false)]
    private int $userId;

    #[Column(type: "decimal", precision: 10, scale: 2, nullable: false)]
    private float $amount = 0.00;

    #[Column(type: "string", length: 3, nullable: false)]
    private string $currency = 'MZN';

    #[Column(type: "datetime", name: "created_at")]
    private DateTimeInterface $createdAt;

    #[Column(type: "datetime", name: "updated_at")]
    private DateTimeInterface $updatedAt;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getAmount(): float
    {
        return $this->amount;
    }
    public function getCurrency(): string
    {
        return $this->currency;
    }
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }
    public function add(float $amount): self
    {
        $this->amount += $amount;
        return $this;
    }
    public function subtract(float $amount): self
    {
        $this->amount -= $amount;
        return $this;
    }
    public function hasEnough(float $amount): bool
    {
        return $this->amount >= $amount;
    }
    public function updateTimestamps(): self
    {
        $this->updatedAt = new \DateTime();
        return $this;
    }
}