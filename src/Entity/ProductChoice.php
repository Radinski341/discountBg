<?php

namespace App\Entity;

use App\Repository\ProductChoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductChoiceRepository::class)]
class ProductChoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $websiteId = null;

    #[ORM\Column(length: 255)]
    private ?string $optionType = null;

    #[ORM\Column(length: 255)]
    private ?string $optionValue = null;

    #[ORM\Column(type: 'text')]
    private ?string $title = null;

    #[ORM\Column(type: 'float')]
    private ?float $oldPrice = null;

    #[ORM\Column(type: 'float')]
    private ?float $originalDiscountPrice = null;

    #[ORM\Column(type: 'float')]
    private ?float $newPrice = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $images = null;

    #[ORM\Column(type: 'float')]
    private ?float $originalDiscountPercent = null;

    #[ORM\Column(type: 'float')]
    private ?float $discountPercent = null;

    #[ORM\ManyToOne(inversedBy: 'productChoices')]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\Column(type: 'boolean')]
    private bool $forDelete;

    #[ORM\OneToMany(mappedBy: 'productChoice', targetEntity: ProductOrder::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $productOrders;

    #[ORM\Column(type: 'text')]
    private ?string $productUrl = null;

    #[ORM\OneToMany(mappedBy: 'productChoice', targetEntity: FavouriteOrder::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $favouriteOrders;

    private bool $isFavourite = false;

    #[ORM\OneToMany(mappedBy: 'productChoice', targetEntity: OrderTransaction::class)]
    private Collection $orderTransactions;

    #[ORM\Column(length: 60)]
    private ?string $websiteName = null;
    public function __construct()
    {
        $this->productOrders = new ArrayCollection();
        $this->favouriteOrders = new ArrayCollection();
        $this->orderTransactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWebsiteId(): ?string
    {
        return $this->websiteId;
    }

    public function setWebsiteId(string $websiteId): static
    {
        $this->websiteId = $websiteId;

        return $this;
    }

    public function getOptionType(): ?string
    {
        return $this->optionType;
    }

    public function setOptionType(string $optionType): static
    {
        $this->optionType = $optionType;

        return $this;
    }

    public function getOptionValue(): ?string
    {
        return $this->optionValue;
    }

    public function setOptionValue(string $optionValue): static
    {
        $this->optionValue = $optionValue;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getOldPrice(): ?float
    {
        return $this->oldPrice;
    }

    public function setOldPrice(float $oldPrice): static
    {
        $this->oldPrice = $oldPrice;

        return $this;
    }

    public function getOriginalDiscountPrice(): ?float
    {
        return $this->originalDiscountPrice;
    }

    public function setOriginalDiscountPrice(float $originalDiscountPrice): static
    {
        $this->originalDiscountPrice = $originalDiscountPrice;

        return $this;
    }

    public function getNewPrice(): ?float
    {
        return $this->newPrice;
    }

    public function setNewPrice(float $newPrice): static
    {
        $this->newPrice = $newPrice;

        return $this;
    }

    public function getImages(): ?string
    {
        return $this->images;
    }

    public function setImages(?string $images): static
    {
        $this->images = $images;

        return $this;
    }

    public function getImagesList(): array
    {
        return explode('|', $this->getImages());
    }

    public function getMainImage()
    {
        return $this->getImagesList()[1];
    }

    public function getOriginalDiscountPercent(): ?float
    {
        return $this->originalDiscountPercent;
    }

    public function setOriginalDiscountPercent(float $originalDiscountPercent): static
    {
        $this->originalDiscountPercent = $originalDiscountPercent;

        return $this;
    }

    public function getDiscountPercent(): ?float
    {
        return $this->discountPercent;
    }

    public function setDiscountPercent(float $discountPercent): static
    {
        $this->discountPercent = $discountPercent;

        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function isForDelete(): bool
    {
        return $this->forDelete;
    }

    public function setForDelete(bool $forDelete): static
    {
        $this->forDelete = $forDelete;

        return $this;
    }

    /**
     * @return Collection<int, ProductOrder>
     */
    public function getProductOrders(): Collection
    {
        return $this->productOrders;
    }

    public function addProductOrder(ProductOrder $productOrder): static
    {
        if (!$this->productOrders->contains($productOrder)) {
            $this->productOrders->add($productOrder);
            $productOrder->setProductChoice($this);
        }

        return $this;
    }

    public function removeProductOrder(ProductOrder $productOrder): static
    {
        if ($this->productOrders->removeElement($productOrder)) {
            // set the owning side to null (unless already changed)
            if ($productOrder->getProductChoice() === $this) {
                $productOrder->setProductChoice(null);
            }
        }

        return $this;
    }

    public function getProductUrl(): ?string
    {
        return $this->productUrl;
    }

    public function setProductUrl(string $productUrl): static
    {
        $this->productUrl = $productUrl;

        return $this;
    }

    /**
     * @return Collection<int, FavouriteOrder>
     */
    public function getFavouriteOrders(): Collection
    {
        return $this->favouriteOrders;
    }

    public function addFavouriteOrder(FavouriteOrder $favouriteOrder): static
    {
        if (!$this->favouriteOrders->contains($favouriteOrder)) {
            $this->favouriteOrders->add($favouriteOrder);
            $favouriteOrder->setProductChoice($this);
        }

        return $this;
    }

    public function removeFavouriteOrder(FavouriteOrder $favouriteOrder): static
    {
        if ($this->favouriteOrders->removeElement($favouriteOrder)) {
            // set the owning side to null (unless already changed)
            if ($favouriteOrder->getProductChoice() === $this) {
                $favouriteOrder->setProductChoice(null);
            }
        }

        return $this;
    }

    public function setIsFavourite(bool $isFavourite): void
    {
        $this->isFavourite = $isFavourite;
    }

    public function getIsFavourite(): bool
    {
        return $this->isFavourite;
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
            $orderTransaction->setProductChoice($this);
        }

        return $this;
    }

    public function removeOrderTransaction(OrderTransaction $orderTransaction): static
    {
        if ($this->orderTransactions->removeElement($orderTransaction)) {
            // set the owning side to null (unless already changed)
            if ($orderTransaction->getProductChoice() === $this) {
                $orderTransaction->setProductChoice(null);
            }
        }

        return $this;
    }

    public function getWebsiteName(): ?string
    {
        return $this->websiteName;
    }

    public function setWebsiteName(string $websiteName): static
    {
        $this->websiteName = $websiteName;

        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
    }
}
