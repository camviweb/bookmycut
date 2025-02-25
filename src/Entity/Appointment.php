<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(length: 255)]
    private ?string $detail = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Service::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?service $service = null;

    /**
     * @var Collection<int, ProductUsage>
     */
    #[ORM\ManyToMany(targetEntity: ProductUsage::class, inversedBy: 'appointments')]
    private Collection $productusage;

    #[ORM\Column]
    private ?int $status = null;

    public function __construct()
    {
        $this->productusage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(string $detail): static
    {
        $this->detail = $detail;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user = null;

        return $this;
    }

    public function getService(): ?service
    {
        return $this->service;
    }

    public function setService(service $service): static
    {
        $this->service = $service;

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
        }

        return $this;
    }

    public function removeProductusage(ProductUsage $productusage): static
    {
        $this->productusage->removeElement($productusage);

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }
}