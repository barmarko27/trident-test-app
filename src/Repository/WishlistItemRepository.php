<?php

namespace App\Repository;

use App\Entity\WishlistItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method WishlistItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method WishlistItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method WishlistItem[]    findAll()
 * @method WishlistItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WishlistItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WishlistItem::class);
    }
}
