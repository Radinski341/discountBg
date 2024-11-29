<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    const STATUS_PENDING = 'PENDING';
    const STATUS_IN_PROGRESS= 'IN PROGRESS';
    const STATUS_RETAKEN = 'RETAKEN';
    const STATUS_ORDERED = 'ORDERED';
    const STATUS_PROBLEM = 'PROBLEM';
    const STATUS_CLOSED = 'CLOSED';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'orderParent', targetEntity: OrderTransaction::class, orphanRemoval: true)]
    private Collection $orderTransactions;

    #[ORM\Column(type: 'float')]
    private ?float $totalPrice = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 30)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'processingOrders')]
    private ?User $takenBy = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $statusDescription = null;

    public function __construct()
    {
        $this->orderTransactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, OrderTransaction>
     */
    public function getOrderTransactions(): Collection
    {
        return $this->orderTransactions;
    }

    public function addOrderTransaction(OrderTransaction $orderTransaction): static
    {
        if (!$this->orderTransactions->contains($orderTransaction)) {
            $this->orderTransactions->add($orderTransaction);
            $orderTransaction->setOrderParent($this);
        }

        return $this;
    }

    public function removeOrderTransaction(OrderTransaction $orderTransaction): static
    {
        if ($this->orderTransactions->removeElement($orderTransaction)) {
            // set the owning side to null (unless already changed)
            if ($orderTransaction->getOrderParent() === $this) {
                $orderTransaction->setOrderParent(null);
            }
        }

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getTakenBy(): ?User
    {
        return $this->takenBy;
    }

    public function setTakenBy(?User $takenBy): static
    {
        $this->takenBy = $takenBy;

        return $this;
    }

    public function getStatusDescription(): ?string
    {
        return $this->statusDescription;
    }

    public function setStatusDescription(?string $statusDescription): static
    {
        $this->statusDescription = $statusDescription;

        return $this;
    }

    public function getStatusStep(): int
    {
        switch ($this->status){
            case self::STATUS_RETAKEN:
            case self::STATUS_PROBLEM:
            case self::STATUS_IN_PROGRESS:
                return 2;
            case self::STATUS_ORDERED:
                return 3;
            case self::STATUS_CLOSED:
                return 4;
            default:
                return 1;
        }
    }
}
