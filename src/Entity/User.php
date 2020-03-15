<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation\Type;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Type("string")
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @Type("string")
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @Type("boolean")
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @Type("ArrayCollection<App\Entity\Wishlist>")
     * @ORM\OneToMany(targetEntity="App\Entity\Wishlist", mappedBy="user", orphanRemoval=true)
     */
    private $wishlists;

    public function __construct()
    {
        $this->wishlists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    /**
     * @return Collection|Wishlist[]
     */
    public function getWishlists(): Collection
    {
        return $this->wishlists;
    }

    public function addWishlist(Wishlist $wishlist): self
    {
        if (!$this->wishlists->contains($wishlist)) {
            $this->wishlists[] = $wishlist;
            $wishlist->setUser($this);
        }

        return $this;
    }

    public function removeWishlist(Wishlist $wishlist): self
    {
        if ($this->wishlists->contains($wishlist)) {
            $this->wishlists->removeElement($wishlist);
            // set the owning side to null (unless already changed)
            if ($wishlist->getUser() === $this) {
                $wishlist->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Generate Random Alphanumeric password.
     *
     * @param int $length Length of password, must be between 8 chars and 255 chars
     * @return string
     * @throws \Exception
     */
    public static function generateRandomPassword(int $length): string
    {
        if($length < 8 || $length > 255) {

            throw new \Exception('Password Length constraint failed! Password must be between 8 chars and 255 chars!');
        }

        $alphaNumericSet = 'abcdefghijklmnopqrstuvwxyzZ1234567890';

        $password = array();

        $alphaLength = strlen($alphaNumericSet) - 1; //prevent to access of non existent array element

        for ($i = 0; $i < $length; $i++) {

            $n = rand(0, $alphaLength);

            $password[] = $alphaNumericSet[$n];

        }

        return implode($password);
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        $this->getEmail();
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }


}
