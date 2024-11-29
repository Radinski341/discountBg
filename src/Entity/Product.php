<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Slug;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(length: 60)]
    private string $websiteName;

    #[ORM\Column(length: 255)]
    private string $websiteUrl;

    #[ORM\Column(length: 255)]
    private string $websiteId;

    #[ORM\Column(type: 'text')]
    private string $productUrl;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(length: 255)]
    #[Slug(fields: ['title'])]
    private ?string $slug = null;

    #[ORM\Column(type: 'float')]
    private float $oldPrice;

    #[ORM\Column(type: 'float')]
    private float $newPrice;

    #[ORM\Column(type: 'float')]
    private float $originalDiscountPrice;

    #[ORM\Column(type: 'float')]
    private float $originalDiscountPercent;

    #[ORM\Column(type: 'float')]
    private float $discountPercent;

    #[ORM\Column(type: 'text')]
    private string $images;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $optionTypes = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $options = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $forDelete = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SubCategory $subCategory = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isActive = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductChoice::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $productChoices;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductOrder::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $productOrders;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: FavouriteOrder::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $favouriteOrders;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: OrderTransaction::class)]
    private Collection $orderTransactions;

    #[ORM\ManyToMany(targetEntity: Carousel::class, mappedBy: 'products')]
    private Collection $inCarousels;

    #[ORM\Column(type: 'float')]
    private float $deliveryPrice = 0;

    private bool $isFavourite = false;

    public function __construct()
    {
        $this->productChoices = new ArrayCollection();
        $this->productOrders = new ArrayCollection();
        $this->favouriteOrders = new ArrayCollection();
        $this->orderTransactions = new ArrayCollection();
        $this->inCarousels = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getWebsiteName(): string
    {
        return $this->websiteName;
    }

    public function setWebsiteName(string $websiteName): static
    {
        $this->websiteName = $websiteName;

        return $this;
    }

    public function getWebsiteUrl(): string
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(string $websiteUrl): static
    {
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    public function setWebsiteId(string $websiteId): static
    {
        $this->websiteId = $websiteId;

        return $this;
    }

    public function getProductUrl(): string
    {
        return $this->productUrl;
    }

    public function setProductUrl(string $productUrl): static
    {
        $this->productUrl = $productUrl;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getOldPrice(): float
    {
        return $this->oldPrice;
    }

    public function setOldPrice(float $oldPrice): static
    {
        $this->oldPrice = $oldPrice;

        return $this;
    }

    public function getNewPrice(): float
    {
        return $this->newPrice;
    }

    public function setNewPrice(float $newPrice): static
    {
        $this->newPrice = $newPrice;

        return $this;
    }

    public function getOriginalDiscountPrice(): float
    {
        return $this->originalDiscountPrice;
    }

    public function setOriginalDiscountPrice(float $originalDiscountPrice): static
    {
        $this->originalDiscountPrice = $originalDiscountPrice;

        return $this;
    }

    public function getOriginalDiscountPercent(): float
    {
        return $this->originalDiscountPercent;
    }

    public function setOriginalDiscountPercent(float $originalDiscountPercent): static
    {
        $this->originalDiscountPercent = $originalDiscountPercent;

        return $this;
    }

    public function getDiscountPercent(): float
    {
        return $this->discountPercent;
    }

    public function setDiscountPercent(float $discountPercent): static
    {
        $this->discountPercent = $discountPercent;

        return $this;
    }

    public function getImages(): string
    {
        return $this->images;
    }

    public function setImages(?string $images): static
    {
        $this->images = $images;

        return $this;
    }

    public function getOptionTypes(): ?string
    {
        return $this->optionTypes;
    }

    public function setOptionTypes(?string $optionTypes): static
    {
        $this->optionTypes = $optionTypes;

        return $this;
    }

    public function getOptions(): ?string
    {
        return $this->options;
    }

    public function setOptions(?string $options): static
    {
        $this->options = $options;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function isForDelete(): ?bool
    {
        return $this->forDelete;
    }

    public function setForDelete(bool $forDelete): static
    {
        $this->forDelete = $forDelete;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getSubCategory(): ?SubCategory
    {
        return $this->subCategory;
    }

    public function setSubCategory(?SubCategory $subCategory): static
    {
        $this->subCategory = $subCategory;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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
    /**
     * @return Collection<int, ProductChoice>
     */
    public function getProductChoices(): Collection
    {
        return $this->productChoices;
    }

    public function addProductChoice(ProductChoice $productChoice): static
    {
        if (!$this->productChoices->contains($productChoice)) {
            $this->productChoices->add($productChoice);
            $productChoice->setProduct($this);
        }

        return $this;
    }

    public function removeProductChoice(ProductChoice $productChoice): static
    {
        if ($this->productChoices->removeElement($productChoice)) {
            // set the owning side to null (unless already changed)
            if ($productChoice->getProduct() === $this) {
                $productChoice->setProduct(null);
            }
        }

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
            $productOrder->setProduct($this);
        }

        return $this;
    }

    public function removeProductOrder(ProductOrder $productOrder): static
    {
        if ($this->productOrders->removeElement($productOrder)) {
            // set the owning side to null (unless already changed)
            if ($productOrder->getProduct() === $this) {
                $productOrder->setProduct(null);
            }
        }

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
            $favouriteOrder->setProduct($this);
        }

        return $this;
    }

    public function removeFavouriteOrder(FavouriteOrder $favouriteOrder): static
    {
        if ($this->favouriteOrders->removeElement($favouriteOrder)) {
            // set the owning side to null (unless already changed)
            if ($favouriteOrder->getProduct() === $this) {
                $favouriteOrder->setProduct(null);
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
            $orderTransaction->setProduct($this);
        }

        return $this;
    }

    public function removeOrderTransaction(OrderTransaction $orderTransaction): static
    {
        if ($this->orderTransactions->removeElement($orderTransaction)) {
            // set the owning side to null (unless already changed)
            if ($orderTransaction->getProduct() === $this) {
                $orderTransaction->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Carousel>
     */
    public function getInCarousels(): Collection
    {
        return $this->inCarousels;
    }

    public function addInCarousel(Carousel $inCarousel): static
    {
        if (!$this->inCarousels->contains($inCarousel)) {
            $this->inCarousels->add($inCarousel);
            $inCarousel->addProduct($this);
        }

        return $this;
    }

    public function removeInCarousel(Carousel $inCarousel): static
    {
        if ($this->inCarousels->removeElement($inCarousel)) {
            $inCarousel->removeProduct($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getDeliveryPrice(): float
    {
        return $this->deliveryPrice;
    }

    public function setDeliveryPrice(float $deliveryPrice): static
    {
        $this->deliveryPrice = $deliveryPrice;

        return $this;
    }
}
