<?php

namespace App\Entity;

use App\Repository\ExchangeHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExchangeHistoryRepository::class)]
class ExchangeHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $convert_from = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $convert_to = null;

    #[ORM\Column]
    private ?float $rate = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $timestamp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getConvertFrom(): ?int
    {
        return $this->convert_from;
    }

    public function setConvertFrom(int $convert_from): self
    {
        $this->convert_from = $convert_from;

        return $this;
    }

    public function getConvertTo(): ?int
    {
        return $this->convert_to;
    }

    public function setConvertTo(int $convert_to): self
    {
        $this->convert_to = $convert_to;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
