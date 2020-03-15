<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Type;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WishlistItemRepository")
 */
class WishlistItem
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
     * @ORM\Column(type="datetime")
     */
    private $creation_date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @Type("App\Entity\Product")
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="wishlistItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    private $desired_price;

    /**
     * @ORM\Column(type="smallint")
     */
    private $quantity;

    /**
     * @Type("App\Entity\Wishlist")
     * @ORM\ManyToOne(targetEntity="App\Entity\Wishlist", inversedBy="wishlistItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $wishlist;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getDesiredPrice(): ?float
    {
        return $this->desired_price;
    }

    public function setDesiredPrice(float $desired_price): self
    {
        $this->desired_price = $desired_price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getWishlist(): ?Wishlist
    {
        return $this->wishlist;
    }

    public function setWishlist(?Wishlist $wishlist): self
    {
        $this->wishlist = $wishlist;

        return $this;
    }
}
