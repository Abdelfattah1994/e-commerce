<?php

namespace App\Repository;

use App\Entity\Order;
use App\Traits\HasCrudOperations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
	use HasCrudOperations;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByDate(\DateTimeInterface $date): array
    {
        $start = new \DateTime($date->format('Y-m-d') . ' 00:00:00');
        $end = new \DateTime($date->format('Y-m-d') . ' 23:59:59');

        return $this->createQueryBuilder('o')
            ->andWhere('o.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    public function findRecent(int $limit = 5): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

	public function search(?string $search = null)
	{
		$qb = $this->createQueryBuilder('o')->join('o.user', 'u');
		if ($search) {
			$qb
				->orWhere('u.firstName LIKE :search')
				->orWhere('u.lastName LIKE :search')
				->orWhere('o.status LIKE :search')
				->orWhere('o.transactionId LIKE :search')
				->setParameter('search', '%' . $search . '%')
			;
		}
		
		return $qb->getQuery()->getResult();
	}
}
