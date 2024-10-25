<?php

namespace App\Repository;

use App\Entity\SiteSettings;
use App\Traits\HasCrudOperations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SiteSettingsRepository extends ServiceEntityRepository
{
	
	use HasCrudOperations;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteSettings::class);
    }
	
    public function findLatest(): ?SiteSettings
    {
        return $this->createQueryBuilder('ss')
            ->orderBy('ss.updatedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
