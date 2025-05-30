<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $Client = null;

    /**
     * @var Collection<int, CartItem>
     */
    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'orderOwn')]
    private Collection $Items;

    #[ORM\Column]
    private ?bool $isConfirmed = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?bool $isDelivred = null;

    #[ORM\Column]
    private ?bool $isReturned = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $deliveryAddress = null;

    public function __construct()
    {
        $this->Items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?User
    {
        return $this->Client;
    }

    public function setClient(?User $Client): static
    {
        $this->Client = $Client;

        return $this;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getItems(): Collection
    {
        return $this->Items;
    }

    public function addItem(CartItem $item): static
    {
        if (!$this->Items->contains($item)) {
            $this->Items->add($item);
            $item->setOrderOwn($this);
        }

        return $this;
    }

    public function removeItem(CartItem $item): static
    {
        if ($this->Items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getOrderOwn() === $this) {
                $item->setOrderOwn(null);
            }
        }

        return $this;
    }

    public function isConfirmed(): ?bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): static
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function updateStatus(): void
    {
        if ($this->isReturned) {
            $this->status = 'Retourné';
        } elseif ($this->isDelivred) {
            $this->status = 'Livrée';
        } elseif ($this->isConfirmed) {
            $this->status = 'En cours de livraison';
        } else {
            $this->status = 'En cours de traitement';
        }
    }

    public function isDelivred(): ?bool
    {
        return $this->isDelivred;
    }

    public function setIsDelivred(bool $isDelivred): static
    {
        $this->isDelivred = $isDelivred;

        return $this;
    }

    public function isReturned(): ?bool
    {
        return $this->isReturned;
    }

    public function setIsReturned(bool $isReturned): static
    {
        $this->isReturned = $isReturned;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): static
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }
}
