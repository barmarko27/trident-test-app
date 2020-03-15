<?php
/**
 * Created by Marco Barrella <marco@barrella.it>.
 * User: marcobarrella
 * Date: 12/03/2020
 * Time: 02:11
 */

namespace App\Service\ApiEntityManager;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\RequestManager\RequestManager;
use App\Service\Validator\ErrorFormatter;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductService extends AbstractApiEntity
{
    /**
     * @var ProductRepository
     */
    private $repository;

    public function __construct(
        ProductRepository $userRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        Security $security,
        RequestManager $requestManager
    ) {

        $this->repository = $userRepository;

        parent::__construct($validator, $serializer, $entityManager, $security, $requestManager);
    }

    public function setEntity()
    {
        new Product();
    }

    /**
     * Create new Product
     * @param array $customData
     * @return Product
     */
    public function create(array $customData = array()): Product
    {
        // Merge request data with any custom data provided
        $data = array_merge($this->getRequestManager()->getPayload(), $customData);

        /**
         * @var $productEntity Product
         */
        $productEntity = $this->getSerializerInterface()->deserialize(json_encode($data), Product::class, 'json');

        $productEntity->setCreationDate(new \DateTime('now'));

        $errors = $this->getValidatorInterface()->validate($productEntity, null, 'create');

        // There is some validation error with entity and set the errors in the error stack.
        if (count($errors) > 0) {

            $this->setLastError(ErrorFormatter::normalizeErrors($errors));

            return $this->getEntity(); // No further operation, return an empty entity!
        }

        try {

            $this->getEntityManagerInterface()->persist($productEntity);

            $this->getEntityManagerInterface()->flush();

            return $productEntity;

        } catch (\Exception $exception) {

            // There is some problem with entity persisting. Errors are set in the error stack.

            $this->setLastError(["message" => $exception->getMessage()]);

            return $this->getEntity(); // No further operation, return an empty entity!
        }
    }

    /**
     * Returns all products
     * @return Product[]
     */
    public function getAll()
    {
        $page = $this->getRequestManager()->getPage();

        $perPage = $this->getRequestManager()->getSize();

        $search = $this->getRequestManager()->getSearch();

        $order = $this->getRequestManager()->getOrder();

        return $this->repository->findBy($search, $order, $perPage, $page);
    }

    /**
     * Get product by ID
     * @param int $id
     * @return Product|null
     */
    public function get(int $id): ?Product
    {
        return $this->repository->findOneBy(['id' => $id]);
    }

    /**
     * Update product by id
     * @param int $id
     * @param array $customData
     * @return Product|null
     */
    public function update(int $id, array $customData = array()): ?Product
    {
        // Merge request data with any custom data provided
        $data = array_merge($this->getRequestManager()->getPayload(), $customData);

        if(! array_key_exists('id', $data) || $data['id'] != $id) {

            $data['id'] = $id;
        }

        /**
         * @var $productEntity Product
         */
        $productEntity = $this->getSerializerInterface()->deserialize(
            json_encode($data),
            Product::class,
            'json'
        );

        $errors = $this->getValidatorInterface()->validate($productEntity, null, 'update');

        // There is some validation error with entity and set the errors in the error stack.
        if (count($errors) > 0) {

            $this->setLastError(ErrorFormatter::normalizeErrors($errors));

            return $this->getEntity(); // No further operation, return an empty entity!
        }

        try {

            $this->getEntityManagerInterface()->flush();

            return $productEntity;

        } catch (\Exception $exception) {

            // There is some problem with entity persisting. Errors are set in the error stack.

            $this->setLastError(["message" => $exception->getMessage()]);

            return $this->getEntity(); // No further operation, return an empty entity!
        }
    }

    /**
     * Delete product by ID
     * @param int $id
     * @return ProductService
     */
    public function delete(int $id): self
    {
        $entity = $this->repository->findOneBy(['id' => $id]);

        if($entity instanceof Product) {

            $this->getEntityManagerInterface()->remove($entity);

            $this->getEntityManagerInterface()->flush();

        } else {

            $this->setLastError(['message' => "Error during deleting product"]);
        }

        return $this;
    }
}