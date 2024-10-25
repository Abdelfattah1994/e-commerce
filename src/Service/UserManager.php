<?php
	
	namespace App\Service;
	
	use App\Entity\User;
	use App\Enums\DatabaseTransaction;
	use App\Repository\UserRepository;
	
	readonly class UserManager
	{
		public function __construct(private UserRepository $userRepository)
		{
		}
		
		public function findAll(?string $search = null): array
		{
			return $this->userRepository->search($search);
		}
		
		public function recents(?int $limit = 5): array
		{
			return $this->userRepository->findRecent($limit);
		}
		
		public function create(User $user): void
		{
			$this->userRepository->save($user, DatabaseTransaction::COMMIT);
		}
		
		public function update(User $user): void
		{
			$this->userRepository->save($user);
		}
		
		public function delete(User $user): void
		{
			$this->userRepository->remove($user, DatabaseTransaction::COMMIT);
		}
		
		public function count(array $criteria = []): int
		{
			return $this->userRepository->count($criteria);
		}
	}
