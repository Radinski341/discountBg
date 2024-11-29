<?php

namespace App\Entity;

use App\Repository\WebsiteDeliveryRoleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WebsiteDeliveryRoleRepository::class)]
class WebsiteDeliveryRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'websiteDeliveryRoles')]
    #[ORM\JoinColumn(nullable: false)]
    private Website $website;

    #[ORM\Column(type: 'float')]
    private float $min;

    #[ORM\Column(type: 'float')]
    private float $max;

    #[ORM\Column(type: 'float')]
    private float $deliveryPrice;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWebsite(): Website
    {
        return $this->website;
    }

    public function setWebsite(Website $website): static
    {
        $this->website = $website;

        return $this;
    }

    public function getMin(): float
    {
        return $this->min;
    }

    public function setMin(float $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): float
    {
        return $this->max;
    }

    public function setMax(float $max): static
    {
        $this->max = $max;

        return $this;
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
