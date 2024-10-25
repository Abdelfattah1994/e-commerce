<?php
	
	namespace App\Repository;
	
	use App\Entity\Category;
	use App\Traits\HasCrudOperations;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\Persistence\ManagerRegistry;
	
	/**
	 * @extends ServiceEntityRepository<Category>
	 */
	class CategoryRepository extends ServiceEntityRepository
	{
		use HasCrudOperations;
		
		public function __construct(ManagerRegistry $registry)
		{
			parent::__construct($registry, Category::class);
		}
		
		public function findActive(): array
		{
			return $this->createQueryBuilder('c')
			            ->andWhere('c.active = :active')
			            ->setParameter('active', true)
			            ->getQuery()
			            ->getResult()
			;
		}
		
		public function search(?string $search = null)
		{
			$qb = $this->createQueryBuilder('c');
			if ($search) {
				$qb
					->andWhere('c.name LIKE :search')
					->setParameter('search', '%' . $search . '%')
				;
			}
			return $qb->getQuery()
			          ->getResult()
			;
		}
	}
