<?php
/**
 * Created by Marco Barrella <marco@barrella.it>.
 * User: marcobarrella
 * Date: 08/03/2020
 * Time: 16:22
 */

namespace App\Service\ApiEntityManager;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

interface ApiEntityManagerInterface
{
    /**
     * @return mixed
     */
    public function getEntity();

    /**
     * @return ValidatorInterface
     */
    public function getValidatorInterface(): ValidatorInterface;

    /**
     * @return SerializerInterface
     */
    public function getSerializerInterface(): SerializerInterface;

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManagerInterface(): EntityManagerInterface;
}