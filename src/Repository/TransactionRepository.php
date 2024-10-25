<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Traits\HasCrudOperations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
	
	use HasCrudOperations;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }
    /**
     * @return Transaction[] Returns an array of Transaction objects
     */
    public function findRecentTransactions(int $limit = 10): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findTransactionsByUser($user, int $limit = 10): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.virtualCard IN (:virtualCards)')
            ->setParameter('virtualCards', $user->getVirtualCards())
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
