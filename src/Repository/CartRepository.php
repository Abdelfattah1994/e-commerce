<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Traits\HasCrudOperations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CartRepository extends ServiceEntityRepository
{
	
	use HasCrudOperations;
	
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function findActiveCartForUser($user): ?Cart
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->andWhere('c.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'active')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
