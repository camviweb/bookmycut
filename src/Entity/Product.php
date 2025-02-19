<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $unitPrice = null;

    /**
     * @var Collection<int, ProductUsage>
     */
    #[ORM\OneToMany(targetEntity: ProductUsage::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $productusage;

    public function __construct()
    {
        $this->productusage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * @return Collection<int, ProductUsage>
     */
    public function getProductusage(): Collection
    {
        return $this->productusage;
    }

    public function addProductusage(ProductUsage $productusage): static
    {
        if (!$this->productusage->contains($productusage)) {
            $this->productusage->add($productusage);
            $productusage->setProduct($this);
        }

        return $this;
    }

    public function removeProductusage(ProductUsage $productusage): static
    {
        if ($this->productusage->removeElement($productusage)) {
            // set the owning side to null (unless already changed)
            if ($productusage->getProduct() === $this) {
                $productusage->setProduct(null);
            }
        }

        return $this;
    }
}
