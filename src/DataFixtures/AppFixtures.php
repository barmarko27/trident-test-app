<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Wishlist;
use App\Entity\WishlistItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $faker;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->faker = Faker::create();

        $this->manager = $manager;

        $this->generateProducts();

        $this->generateMasterUser();

        $this->generateUsers();

        $this->generateWishlists();

        $this->generateWishlistItems();
    }

    /**
     * Generate fake products
     * @param int|null $numOfProducts
     */
    private function generateProducts(int $numOfProducts = null)
    {
        $numOfProducts = $numOfProducts ?? rand(1, 10);

        $populator = new \Faker\ORM\Doctrine\Populator($this->faker, $this->manager);

        $faker = $this->faker;

        $populator->addEntity(Product::class, $numOfProducts, array(

            'ean' => function() use ($faker) { return $faker->ean13(); },

            'thumbnail' => function() use ($faker) { return $faker->imageUrl(300, 300); },

            'price' => function() { return rand(1, 999); },

        ));

        $populator->execute();
    }

    /**
     * Generate fake user
     * @param int|null $numOfUsers
     * @throws \Exception
     */
    private function generateUsers(int $numOfUsers = null)
    {
        $numOfUsers = $numOfUsers ?? rand(1, 10);

        for ($i = 0; $i < $numOfUsers; ++$i) {

            $user = new User();

            $user->setStatus(1);

            $user->setEmail($this->faker->email);

            $password = $this->userPasswordEncoder->encodePassword($user, $user->generateRandomPassword(rand(8, 15)));

            $user->setPassword($password);

            $this->manager->persist($user);
        }

        $this->manager->flush();
    }

    private function generateMasterUser()
    {
        $user = new User();

        $user->setStatus(1);

        $user->setEmail('admin@trident.test.local');

        $password = $this->userPasswordEncoder->encodePassword($user, 'trident_test_password');

        $user->setPassword($password);

        $this->manager->persist($user);

        $this->manager->flush();
    }

    /**
     * Generate fake wishlist
     * @param int|null $numOfWishlists
     */
    private function generateWishlists(int $numOfWishlists = null)
    {
        $numOfWishlists = $numOfWishlists ?? rand(1, 10);

        $users = $this->manager->getRepository(User::class)->findBy([], [], rand(1, 5), 0);

        for ($i = 0; $i < $numOfWishlists; $i++) {

            $wishlist = new Wishlist();

            $wishlist->setStatus(1);

            $wishlist->setCreationDate(new \DateTime('now'));

            $wishlist->setName($this->faker->name);

            $wishlist->setUser($users[rand(0, count($users) - 1)]);

            $this->manager->persist($wishlist);
        }

        $this->manager->flush();
    }

    /**
     * Generate fake wishlist items
     * @param int|null $numOfWishListItems
     */
    private function generateWishlistItems(int $numOfWishListItems = null)
    {
        $numOfWishListItems = $numOfWishListItems ?? rand(1, 10);

        $products = $this->manager->getRepository(Product::class)->findBy([], [], rand(1, 10), 0);

        $wishLists = $this->manager->getRepository(Wishlist::class)->findBy([], [], rand(1, 10), 0);

        for ($i = 0; $i < $numOfWishListItems; $i++) {

            $wishListItem = new WishlistItem();

            $wishListItem->setStatus(1);

            $wishListItem->setCreationDate(new \DateTime('now'));

            $wishListItem->setProduct($products[rand(0, count($products) - 1)]);

            $wishListItem->setWishlist($wishLists[rand(0, count($wishLists) - 1)]);

            $wishListItem->setDesiredPrice(rand(1.00, 999.99));

            $wishListItem->setQuantity(rand(1, 99));

            $this->manager->persist($wishListItem);
        }

        $this->manager->flush();
    }
}
