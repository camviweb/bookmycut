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

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $user;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?service $service = null;

    /**
     * @var Collection<int, ProductUsage>
     */
    #[ORM\ManyToMany(targetEntity: ProductUsage::class, inversedBy: 'appointments')]
    private Collection $productusage;

    public function __construct()
    {
        $this->user = new ArrayCollection();
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

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

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
}
