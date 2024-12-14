<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "Моля въведете е-мейл")]
    #[Assert\Email(message: "Моля въведете валиден е-мейл")]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[Assert\NotBlank(message: "Моля въведете парола", groups: ['registration'])]
    #[Assert\Length(min: 8, minMessage: "Паролата мора да е минимум 8 карактера", groups: ['registration'])]
    private string $plainPassword;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Cart $cart = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Favourite $favourite = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class, orphanRemoval: true)]
    private Collection $orders;

    #[ORM\OneToMany(mappedBy: 'takenBy', targetEntity: Order::class)]
    private Collection $processingOrders;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Моля въведете телефонен номер")]
    #[Assert\Regex(pattern: "/^\+359 ?\d{3} ?\d{3} ?\d{3}$|^0\d{3} ?\d{3} ?\d{3}$/", message: "Моля внесете валиден телефонен номер в некой од следващите формати: +359 888 123 456, +359888123456, 0888 123 456, 0888123456")]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Моля въведете адрес")]
    #[Assert\Length(min: 10, max: 255, minMessage: ' Адреса не може да е по малку од 10 карактера', maxMessage: 'Адреса не може да е повече од 255 карактера')]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Моля изберете град")]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $populatedPlace = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Моля въведете вашите имена")]
    #[Assert\Length(min: 5, max: 255, minMessage: 'Имената не може да са по малку од 5 карактера', maxMessage: 'Имената не може да са повече од 255 карактера')]
    private ?string $fullName = null;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->processingOrders = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(Cart $cart): static
    {
        // set the owning side of the relation if necessary
        if ($cart->getUser() !== $this) {
            $cart->setUser($this);
        }

        $this->cart = $cart;

        return $this;
    }

    public function getFavourite(): ?Favourite
    {
        return $this->favourite;
    }

    public function setFavourite(Favourite $favourite): static
    {
        // set the owning side of the relation if necessary
        if ($favourite->getUser() !== $this) {
            $favourite->setUser($this);
        }

        $this->favourite = $favourite;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getProcessingOrders(): Collection
    {
        return $this->processingOrders;
    }

    public function addProcessingOrder(Order $processingOrder): static
    {
        if (!$this->processingOrders->contains($processingOrder)) {
            $this->processingOrders->add($processingOrder);
            $processingOrder->setTakenBy($this);
        }

        return $this;
    }

    public function removeProcessingOrder(Order $processingOrder): static
    {
        if ($this->processingOrders->removeElement($processingOrder)) {
            // set the owning side to null (unless already changed)
            if ($processingOrder->getTakenBy() === $this) {
                $processingOrder->setTakenBy(null);
            }
        }

        return $this;
    }



    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPopulatedPlace(): ?string
    {
        return $this->populatedPlace;
    }

    public function setPopulatedPlace(string $populatedPlace): static
    {
        $this->populatedPlace = $populatedPlace;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

}
