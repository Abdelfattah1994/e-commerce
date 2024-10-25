<?php

namespace App\Repository;

use App\Entity\SupportTicket;
use App\Traits\HasCrudOperations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class SupportTicketRepository extends ServiceEntityRepository
{
	
	use HasCrudOperations;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SupportTicket::class);
    }
    public function search(?string $search = null, int $limit = 50): array
    {
         $qb = $this->createQueryBuilder('st')
	         ->join('st.user', 'u')
	         ;
	    
	    if ($search) {
		    $qb
			    ->orWhere('st.subject LIKE :search')
			    ->orWhere('st.message LIKE :search')
			    ->orWhere('st.status LIKE :search')
			    ->orWhere('u.lastName LIKE :search')
			    ->orWhere('u.firstName LIKE :search')
			    ->orWhere('u.email LIKE :search')
			    ->setParameter('search', '%' . $search . '%')
		    ;
	    }
		 
           return $qb->orderBy('st.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
	
	public function findByUser(UserInterface $user)
	{
		return $this->createQueryBuilder('st')
		     ->andWhere('st.user = :user')
		     ->andWhere('st.status = :status')
		     ->setParameter('status', 'OPEN')
		     ->setParameter('user', $user)
		     ->orderBy('st.createdAt', 'DESC')
		     ->getQuery()
		     ->getResult();
	}
}
