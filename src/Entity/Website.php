<?php

namespace App\Entity;

use App\Repository\WebsiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WebsiteRepository::class)]
class Website
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $websiteName = null;

    #[ORM\OneToMany(mappedBy: 'website', targetEntity: WebsiteDeliveryRole::class, orphanRemoval: true)]
    private Collection $websiteDeliveryRoles;

    #[ORM\Column(type: 'float')]
    private float $freeDeliveryOver = 0;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $processedAt = null;

    public function __construct()
    {
        $this->websiteDeliveryRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, WebsiteDeliveryRole>
     */
    public function getWebsiteDeliveryRoles(): Collection
    {
        return $this->websiteDeliveryRoles;
    }

    public function addWebsiteDeliveryRole(WebsiteDeliveryRole $websiteDeliveryRole): static
    {
        if (!$this->websiteDeliveryRoles->contains($websiteDeliveryRole)) {
            $this->websiteDeliveryRoles->add($websiteDeliveryRole);
            $websiteDeliveryRole->setWebsite($this);
        }

        return $this;
    }

    public function removeWebsiteDeliveryRole(WebsiteDeliveryRole $websiteDeliveryRole): static
    {
        if ($this->websiteDeliveryRoles->removeElement($websiteDeliveryRole)) {
            // set the owning side to null (unless already changed)
            if ($websiteDeliveryRole->getWebsite() === $this) {
                $websiteDeliveryRole->setWebsite(null);
            }
        }

        return $this;
    }

    public function getFreeDeliveryOver(): float
    {
        return $this->freeDeliveryOver;
    }

    public function setFreeDeliveryOver(float $freeDeliveryOver): static
    {
        $this->freeDeliveryOver = $freeDeliveryOver;

        return $this;
    }

    public function __toString(): string
    {
        return $this->websiteName;
    }

    public function getProcessedAt(): ?\DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function setProcessedAt(?\DateTimeImmutable $processedAt): static
    {
        $this->processedAt = $processedAt;

        return $this;
    }
}
