<?php

namespace App\Entity;

use App\Repository\FavouriteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavouriteRepository::class)]
class Favourite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'favourite', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'favourite', targetEntity: FavouriteOrder::class, orphanRemoval: true)]
    private Collection $favouriteOrders;

    public function __construct()
    {
        $this->favouriteOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

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
            $favouriteOrder->setFavourite($this);
        }

        return $this;
    }

    public function removeFavouriteOrder(FavouriteOrder $favouriteOrder): static
    {
        if ($this->favouriteOrders->removeElement($favouriteOrder)) {
            // set the owning side to null (unless already changed)
            if ($favouriteOrder->getFavourite() === $this) {
                $favouriteOrder->setFavourite(null);
            }
        }

        return $this;
    }
}
