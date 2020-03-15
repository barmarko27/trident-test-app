<?php
/**
 * Created by Marco Barrella <marco@barrella.it>.
 * User: marcobarrella
 * Date: 12/03/2020
 * Time: 22:44
 */

namespace App\Service\ApiEntityManager;


use App\Repository\WishlistRepository;
use App\Service\RequestManager\RequestManager;
use App\Service\Validator\ErrorFormatter;
use App\Entity\Wishlist;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WishlistService extends AbstractApiEntity
{
    /**
     * @var WishlistRepository
     */
    private $repository;

    public function __construct(
        WishlistRepository $wishlistRepository,
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
     * @return Wishlist
     */
    public function setEntity()
    {
        return new Wishlist();
    }

    /**
     * Create new Wishlist of current user
     * @param array $customData
     * @return Wishlist
     */
    public function create(array $customData = array()): Wishlist
    {
        // Merge request data with any custom data provided
        $data = array_merge($this->getRequestManager()->getPayload(), $customData);

        /**
         * @var $wishListEntity Wishlist
         */
        $wishListEntity = $this->getSerializerInterface()->deserialize(json_encode($data), Wishlist::class, 'json');

        $wishListEntity->setStatus(Wishlist::STATUS_ACTIVE); // Set wishlist status to active by default

        $wishListEntity->setCreationDate(new \DateTime('now'));

        $wishListEntity->setUser($this->getLoggedUser());// Set current logged user to the wishlist.

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
     * Returns all wishlist belong current user
     * @return Wishlist[]
     */
    public function getAll()
    {
        $page = $this->getRequestManager()->getPage();

        $perPage = $this->getRequestManager()->getSize();

        $search = $this->getRequestManager()->getSearch();

        $order = $this->getRequestManager()->getOrder();

        $search['user'] = $this->getLoggedUser()->getId(); // Ensure that wishlist are all belong current user

        return $this->repository->findBy($search, $order, $perPage, $page);
    }

    /**
     * Get wishlist by ID
     * @param int $id
     * @return Wishlist|null
     */
    public function get(int $id): ?Wishlist
    {
        $search = [];

        $search['id'] = $id;

        $search['user'] = $this->getLoggedUser()->getId(); // Ensure that wishlist are all belong current user

        return $this->repository->findOneBy($search);
    }

    /**
     * Update wishlist by id
     * @param int $id
     * @param array $customData
     * @return Wishlist|null
     */
    public function update(int $id, array $customData = array()): ?Wishlist
    {
        // Merge request data with any custom data provided
        $data = array_merge($this->getRequestManager()->getPayload(), $customData);

        if(! array_key_exists('id', $data) || $data['id'] != $id) {

            $data['id'] = $id;
        }

        unset($data['user']);

        /**
         * @var $wishListEntity Wishlist
         */
        $wishListEntity = $this->getSerializerInterface()->deserialize(
            json_encode($data),
            Wishlist::class,
            'json'
        );

        $errors = $this->getValidatorInterface()->validate($wishListEntity, null, 'update');

        // There is some validation error with entity and set the errors in the error stack.
        if (count($errors) > 0) {

            $this->setLastError(ErrorFormatter::normalizeErrors($errors));

            return $this->getEntity(); // No further operation, return an empty entity!
        }

        // If this entity is not of current user, return an error
        if($wishListEntity->getUser()->getId() != $this->getLoggedUser()->getId()) {

            $this->setLastError(['message' => 'Cannot edit this entity!']);

            return $this->getEntity(); // No further operation, return an empty entity!
        }

        try {

            $this->getEntityManagerInterface()->flush();

            return $wishListEntity;

        } catch (\Exception $exception) {

            // There is some problem with entity persisting. Errors are set in the error stack.

            $this->setLastError(["message" => $exception->getMessage()]);

            return $this->getEntity(); // No further operation, return an empty entity!
        }
    }

    /**
     * Delete wishlist by ID
     * @param int $id
     * @return WishlistService
     */
    public function delete(int $id): self
    {
        $search = [];

        $search['id'] = $id;

        $search['user'] = $this->getLoggedUser()->getId(); // Ensure that wishlist are all belong current user

        $entity = $this->repository->findOneBy($search);

        if($entity instanceof Wishlist) {

            $this->getEntityManagerInterface()->remove($entity);

            $this->getEntityManagerInterface()->flush();

        } else {

            $this->setLastError(['message' => "Error during deleting wishlist"]);
        }

        return $this;
    }
}