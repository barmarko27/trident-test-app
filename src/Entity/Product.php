<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Accessor;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
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
     *      minMessage = "Product name must be at least {{ limit }} characters long",
     *      maxMessage = "Product name cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Assert\NotBlank(groups={"create"})
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "Product description must be at least {{ limit }} characters long",
     * )
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @Assert\NotBlank(groups={"create"})
     * @Assert\Isbn(
     *     type = "isbn13",
     *     message = "This value is not  valid."
     * )
     * @ORM\Column(type="string", length=13)
     */
    private $ean;

    /**
     * @Assert\NotBlank(groups={"create"})
     * @Assert\Url(
     *    message = "The url '{{ value }}' is not a valid url",
     * )
     * @ORM\Column(type="text")
     */
    private $thumbnail;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WishlistItem", mappedBy="product")
     */
    private $wishlistItems;

    /**
     * @Assert\Positive
     * @Assert\NotBlank(groups={"create"})
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    private $price;

    /**
     * @Assert\NotBlank(groups={"create"})
     * @Assert\DateTime
     * @ORM\Column(type="datetime")
     */
    private $creation_date;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function setEan(string $ean): self
    {
        $this->ean = $ean;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
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
            $wishlistItem->setProduct($this);
        }

        return $this;
    }

    public function removeWishlistItem(WishlistItem $wishlistItem): self
    {
        if ($this->wishlistItems->contains($wishlistItem)) {
            $this->wishlistItems->removeElement($wishlistItem);
            // set the owning side to null (unless already changed)
            if ($wishlistItem->getProduct() === $this) {
                $wishlistItem->setProduct(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

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
}
