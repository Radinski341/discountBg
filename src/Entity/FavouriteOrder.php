<?php

namespace App\Entity;

use App\Repository\FavouriteOrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavouriteOrderRepository::class)]
class FavouriteOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'favouriteOrders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'favouriteOrders')]
    private ?ProductChoice $productChoice = null;

    #[ORM\ManyToOne(inversedBy: 'favouriteOrders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Favourite $favourite = null;

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

    public function getFavourite(): ?Favourite
    {
        return $this->favourite;
    }

    public function setFavourite(?Favourite $favourite): static
    {
        $this->favourite = $favourite;

        return $this;
    }
}
