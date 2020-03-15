<?php
/**
 * Created by Marco Barrella <marco@barrella.it>.
 * User: marcobarrella
 * Date: 08/03/2020
 * Time: 16:25
 */

namespace App\Service\ApiEntityManager;


use App\Service\RequestManager\RequestManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractApiEntity implements ApiEntityManagerInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private $entity;

    private $security;

    private $lastErrors = array();

    private $requestManager;

    public function __construct(
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        Security $security,
        RequestManager $requestManager
    ) {

        $this->validator = $validator;

        $this->serializer = $serializer;

        $this->entityManager = $entityManager;

        $this->security = $security;

        $this->requestManager = $requestManager;
    }

    abstract public function setEntity();

    /**
     * Get default entity
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity ?? $this->setEntity();
    }

    /**
     * @inheritDoc
     */
    public function getValidatorInterface(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * @inheritDoc
     */
    public function getSerializerInterface(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @inheritDoc
     */
    public function getEntityManagerInterface(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * Set last error in error stack
     * @param array $error
     */
    protected function setLastError(array $error)
    {
       $this->lastErrors[] = $error;
    }

    /**
     * Get last errors
     * @inheritDoc
     */
    public function getLastErrors(): array
    {
        return $this->lastErrors;
    }

    /**
     * Get logged in User
     * @return UserInterface
     */
    protected function getLoggedUser(): UserInterface
    {
        return $this->security->getUser();
    }

    /**
     * @return RequestManager
     */
    protected function getRequestManager(): RequestManager
    {
        return $this->requestManager;
    }
}