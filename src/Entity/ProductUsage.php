<?php

namespace App\Entity;

use App\Repository\ProductUsageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductUsageRepository::class)]
class ProductUsage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantityUsed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantityUsed(): ?int
    {
        return $this->quantityUsed;
    }

    public function setQuantityUsed(int $quantityUsed): static
    {
        $this->quantityUsed = $quantityUsed;

        return $this;
    }
}
