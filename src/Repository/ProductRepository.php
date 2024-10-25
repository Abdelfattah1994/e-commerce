<?php

namespace App\Repository;

use App\Entity\Product;
use App\Traits\HasCrudOperations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
	use HasCrudOperations;
	
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
	
	public function findByCategory(int $categoryId): array
	{
		return $this->createQueryBuilder('p')
		            ->join('p.category', 'c')
		            // TODO: Il faut ajouter ici le filtre necessaire pour recuperer que les produits qui sont liés à la categorie passée en paramètre
                    ->andWhere('c.id = :categoryId')
                    ->setParameter('categoryId', $categoryId)
		            ->getQuery()
		            ->getResult();
	}
	public function findFeatured(?int $limit = 3): array
	{
		return $this->createQueryBuilder('p')
					// TODO: Il faut ajouter un filtre ici pour récuperer les produits qui sont en featured
                    ->andWhere('p.featured = :featured')
                    ->setParameter('featured', true)
		            ->setMaxResults($limit)
		            ->getQuery()
		            ->getResult();
	}
	public function getFilteredProducts(?string $search = null, ?int $category = null): array
	{
		$qb = $this->createQueryBuilder('p')
		           ->andWhere('p.active = :active')
		           ->setParameter('active', true);
		
		if ($search) {
			$qb->andWhere('p.name LIKE :search OR p.description LIKE :search')
			   ->setParameter('search', '%' . $search . '%');
		}
		
		if ($category) {
			$qb
				->join('p.category', 'c')
				->andWhere('p.category = :category')
				->andWhere('c.active = :active')
			   ->setParameter('category', $category)
			   ->setParameter('active', true);
		}
		
		
		return $qb->getQuery()->getResult();
	}
	public function findSimilarProducts(Product $product, int $limit = 4): array
	{
		return $this->createQueryBuilder('p')
		            ->andWhere('p.category = :category')
		            ->andWhere('p.id != :id')
		            ->setParameter('category', $product->getCategory())
		            ->setParameter('id', $product->getId())
		            ->setMaxResults($limit)
		            ->getQuery()
		            ->getResult();
	}
	
	public function findLowStockProducts(int $threshold): array
	{
		return $this->createQueryBuilder('p')
		            ->andWhere('p.stock <= :threshold')
		            ->setParameter('threshold', $threshold)
		            ->getQuery()
		            ->getResult();
	}
	
	public function findTopSelling(int $limit): array
	{
		return $this->createQueryBuilder('p')
		            ->select('p.id', 'p.name', 'p.price', 'p.stock', 'SUM(oi.quantity) as totalSold')
		            ->leftJoin('p.orderItems', 'oi')
		            ->groupBy('p.id')
		            ->orderBy('totalSold', 'DESC')
		            ->setMaxResults($limit)
		            ->getQuery()
		            ->getResult();
	}
	
	public function search(?string $search = null)
	{
		$qb = $this->createQueryBuilder('p')->join('p.category', 'c');
		if ($search) {
			$qb
				->orWhere('p.name LIKE :search')
				->orWhere('p.description LIKE :search')
				->orWhere('c.name LIKE :search')
				->setParameter('search', '%' . $search . '%')
			;
		}
		
		return $qb->getQuery()->getResult();
	}
}
