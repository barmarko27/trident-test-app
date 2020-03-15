<?php
/**
 * Created by Marco Barrella <marco@barrella.it>.
 * User: marcobarrella
 * Date: 12/03/2020
 * Time: 22:44
 */

namespace App\Service\ApiEntityManager;


use App\Entity\Wishlist;
use App\Repository\WishlistItemRepository;
use App\Service\RequestManager\RequestManager;
use App\Service\Validator\ErrorFormatter;
use App\Entity\WishlistItem;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WishlistItemService extends AbstractApiEntity
{
    /**
     * @var WishlistItemRepository
     */
    private $repository;

    public function __construct(
        WishlistItemRepository $wishlistRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        Security $security,
        RequestManager $requestManager
    ) {

        $this->repository = $wishlistRepository;

        parent::__construct($validator, $serializer, $entityManager, $security, $requestManager);
    }

    /**
     * @return WishlistItem
     */
    public function setEntity()
    {
        return new WishlistItem();
    }

    /**
     * Create new WishlistItem of current user
     * @param array $customData
     * @return WishlistItem
     */
    public function create(array $customData = array()): WishlistItem
    {
        // Merge request data with any custom data provided
        $data = array_merge($this->getRequestManager()->getPayload(), $customData);

        /**
         * @var $wishListEntity WishlistItem
         */
        $wishListEntity = $this->getSerializerInterface()->deserialize(json_encode($data), WishlistItem::class, 'json');

        $wishListEntity->setStatus(WishlistItem::STATUS_ACTIVE); // Set wishlist status to active by default

        $wishListEntity->setCreationDate(new \DateTime('now'));

        $errors = $this->getValidatorInterface()->validate($wishListEntity, null, 'create');

        // There is some validation error with entity and set the errors in the error stack.
        if (count($errors) > 0) {

            $this->setLastError(ErrorFormatter::normalizeErrors($errors));

            return $this->getEntity(); // No further operation, return an empty entity!
        }

        try {

            $this->getEntityManagerInterface()->persist($wishListEntity);

            $this->getEntityManagerInterface()->flush();

            return $wishListEntity;

        } catch (\Exception $exception) {

            // There is some problem with entity persisting. Errors are set in the error stack.

            $this->setLastError(["message" => $exception->getMessage()]);

            return $this->getEntity(); // No further operation, return an empty entity!
        }
    }

    /**
     * Returns all wishlist items belong current wishlist
     * @param int $wishListid
     * @return WishlistItem[]
     */
    public function getAll(int $wishListid)
    {
        $wishList = $this->getEntityManagerInterface()->getRepository(Wishlist::class)->find($wishListid);

        // Check if wishlist exist and its belong current user

        if(! $wishList instanceof Wishlist || $wishList->getUser()->getId() != $this->getLoggedUser()->getId()) {

            // There is some problem with entity or relations. Errors are set in the error stack.

            $this->setLastError(["message" => 'Wishlist not exists or is not of current user']);

            return $this->getEntity(); // No further operation, return an empty entity!
        }

        $page = $this->getRequestManager()->getPage();

        $perPage = $this->getRequestManager()->getSize();

        $search = $this->getRequestManager()->getSearch();

        $order = $this->getRequestManager()->getOrder();

        $search['wishlist'] = $wishListid;

        return $this->repository->findBy($search, $order, $perPage, $page);
    }

    /**
     * Get wishlist item
     * @param int $wishListid
     * @param int $id
     * @return WishlistItem|null
     */
    public function get(int $wishListid, int $id): ?WishlistItem
    {
        $search = [];

        $search['id'] = $id;

        $search['wishlist']['id'] = $wishListid; // Ensure that wishlist are all belong current user

        $wishListItem = $this->repository->findOneBy($search);

        // Check if wishlist exist and its belong current user

        if(! $wishListItem instanceof WishlistItem || $wishListItem->getWishlist()->getUser()->getId() != $this->getLoggedUser()->getId()) {

            // There is some problem with entity or relations. Errors are set in the error stack.

            $this->setLastError(["message" => 'Wishlist item is not belong your wishlist']);

            return $this->getEntity(); // No further operation, return an empty entity!
        }

        return $wishListItem;
    }

    /**
     * Update wishlist item by id
     * @param int $wishlistID
     * @param int $id
     * @param array $customData
     * @return WishlistItem|null
     */
    public function update(int $wishlistID, int $id, array $customData = array()): ?WishlistItem
    {
        // Merge request data with any custom data provided
        $data = array_merge($this->getRequestManager()->getPayload(), $customData);

        if(! array_key_exists('id', $data) || $data['id'] != $id) {

            $data['id'] = $id;
        }

        unset($data['user']);

        $data['wishlist']['id'] = $wishlistID;

        /**
         * @var $wishListItemEntity WishlistItem
         */
        $wishListItemEntity = $this->getSerializerInterface()->deserialize(
            json_encode($data),
            WishlistItem::class,
            'json'
        );

        if(! $wishListItemEntity instanceof WishlistItem || $wishListItemEntity->getWishlist()->getUser()->getId() != $this->getLoggedUser()->getId()) {

            // There is some problem with entity or relations. Errors are set in the error stack.

            $this->setLastError(["message" => 'Wishlist item is not belong your wishlist']);

            return $this->getEntity(); // No further operation, return an empty entity!
        }

        $errors = $this->getValidatorInterface()->validate($wishListItemEntity, null, 'update');

        // There is some validation error with entity and set the errors in the error stack.
        if (count($errors) > 0) {

            $this->setLastError(ErrorFormatter::normalizeErrors($errors));

            return $this->getEntity(); // No further operation, return an empty entity!
        }

        try {

            $this->getEntityManagerInterface()->flush();

            return $wishListItemEntity;

        } catch (\Exception $exception) {

            // There is some problem with entity persisting. Errors are set in the error stack.

            $this->setLastError(["message" => $exception->getMessage()]);

            return $this->getEntity(); // No further operation, return an empty entity!
        }
    }

    /**
     * Delete wishlist item by ID
     * @param int $id
     * @return WishlistItemService
     */
    public function delete(int $wishlistID, int $id): self
    {
        $search = [];

        $search['id'] = $id;

        $search['wishlist']['id'] = $wishlistID;

        $entity = $this->repository->findOneBy($search);

        if($entity instanceof WishlistItem && $entity->getWishlist()->getUser()->getId() == $this->getLoggedUser()->getId()) {

            $this->getEntityManagerInterface()->remove($entity);

            $this->getEntityManagerInterface()->flush();

        } else {

            $this->setLastError(['message' => "Deleting wishlist item error"]);
        }

        return $this;
    }
}