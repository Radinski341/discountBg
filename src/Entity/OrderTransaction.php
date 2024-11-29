<?php

namespace App\Entity;

use App\Repository\OrderTransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderTransactionRepository::class)]
class OrderTransaction
{
    const STATUS_PENDING = 'PENDING';
    const STATUS_ORDERED = 'ORDERED';
    const STATUS_DELIVERED = 'DELIVERED';
    const STATUS_FOR_RETURN = 'FOR RETURN';
    const STATUS_RETURNED = 'RETURNED';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderTransactions')]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'orderTransactions')]
    private ?ProductChoice $productChoice = null;

    #[ORM\Column(type: 'text')]
    private ?string $originalWebsiteUrl = null;

    #[ORM\Column(type: 'integer')]
    private ?int $quantity = null;

    #[ORM\Column(type: 'float')]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'orderTransactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $orderParent = null;

    #[ORM\Column(length: 30, nullable: false)]
    private ?string $status = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $statusDescription = null;

    #[ORM\Column(type: 'text')]
    private ?string $productImage = null;

    #[ORM\Column(length: 255)]
    private ?string $productTitle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getProductChoice(): ?ProductChoice
    {
        return $this->productChoice;
    }

    public function setProductChoice(?ProductChoice $productChoice): static
    {
        $this->productChoice = $productChoice;

        return $this;
    }

    public function getOriginalWebsiteUrl(): ?string
    {
        return $this->originalWebsiteUrl;
    }

    public function setOriginalWebsiteUrl(string $originalWebsiteUrl): static
    {
        $this->originalWebsiteUrl = $originalWebsiteUrl;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getOrderParent(): ?Order
    {
        return $this->orderParent;
    }

    public function setOrderParent(?Order $orderParent): static
    {
        $this->orderParent = $orderParent;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

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

    public function getProductImage(): ?string
    {
        return $this->productImage;
    }

    public function setProductImage(string $productImage): static
    {
        $this->productImage = $productImage;

        return $this;
    }

    public function getStatusInPercent()
    {
        switch ($this->status){
            case self::STATUS_PENDING:
                if($this->orderParent->getStatus() !== Order::STATUS_PENDING){
                    return 20;
                }
                return 0;
            case self::STATUS_ORDERED:
            case self::STATUS_FOR_RETURN:
                return 50;
            case self::STATUS_DELIVERED:
            case self::STATUS_RETURNED:
                return 100;

        }
    }

    public function getProductTitle(): ?string
    {
        return $this->productTitle;
    }

    public function setProductTitle(string $productTitle): static
    {
        $this->productTitle = $productTitle;

        return $this;
    }
}
