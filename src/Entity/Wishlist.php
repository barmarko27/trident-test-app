<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Accessor;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WishlistRepository")
 */
class Wishlist
{
    const STATUS_ACTIVE = 1;

    const STATUS_DELETED = 0;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(groups={"create"})
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "Wishlist name must be at least {{ limit }} characters long",
     *      maxMessage = "Wishlist name cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Assert\NotBlank(groups={"create"})
     * @Assert\DateTime
     * @ORM\Column(type="datetime")
     */
    private $creation_date;

    /**
     * @Assert\NotBlank(groups={"create"})
     * @Assert\Choice(callback="getAvailableStatuses", message="Status of wishlist not allowed")
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @Type("integer")
     * @Accessor(getter="getUserSerializedType")
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="wishlists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WishlistItem", mappedBy="wishlist", orphanRemoval=true)
     */
    private $wishlistItems;

    public function __construct()
    {
        $this->wishlistItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date): self
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUserSerializedType(): ?int
    {
        return $this->user->getId();
    }

    /**
     * @return Collection|WishlistItem[]
     */
    public function getWishlistItems(): Collection
    {
        return $this->wishlistItems;
    }

    public function addWishlistItem(WishlistItem $wishlistItem): self
    {
        if (!$this->wishlistItems->contains($wishlistItem)) {
            $this->wishlistItems[] = $wishlistItem;
            $wishlistItem->setWishlist($this);
        }

        return $this;
    }

    public function removeWishlistItem(WishlistItem $wishlistItem): self
    {
        if ($this->wishlistItems->contains($wishlistItem)) {
            $this->wishlistItems->removeElement($wishlistItem);
            // set the owning side to null (unless already changed)
            if ($wishlistItem->getWishlist() === $this) {
                $wishlistItem->setWishlist(null);
            }
        }

        return $this;
    }

    public static function getAvailableStatuses(): array
    {
        return [self::STATUS_ACTIVE, self::STATUS_DELETED];
    }
}
