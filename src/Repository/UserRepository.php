<?php

namespace App\Repository;

use App\Entity\User;
use App\Traits\HasCrudOperations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use function dd;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
	use HasCrudOperations;
	
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
	
	public function findRecent(int $limit): array
	{
		return $this->createQueryBuilder('u')
					->select('u.id', 'u.lastName', 'u.firstName', 'u.email', 'u.roles')
		            ->orderBy('u.createdAt', 'DESC')
		            ->setMaxResults($limit)
		            ->getQuery()
		            ->getResult();
	}
	
	public function search(?string $search = null)
	{
		$qb = $this->createQueryBuilder('u')
		           ->select('u.id', 'u.lastName', 'u.firstName', 'u.email', 'u.roles');
		
		if ($search) {
			$qb
				->orWhere('u.lastName LIKE :search')
				->orWhere('u.firstName LIKE :search')
				->orWhere('u.email LIKE :search')
				->setParameter('search', '%' . $search . '%')
			;
		}
		
		return $qb->orderBy('u.createdAt', 'DESC')
		          ->getQuery()
		          ->getResult();
	}
}
