<?php
	
	namespace App\Service;
	
	use App\Entity\Category;
	use App\Enums\DatabaseTransaction;
	use App\Repository\CategoryRepository;
	
	readonly class CategoryManager
	{
		public function __construct(private CategoryRepository $categoryRepository)
		{
		}
		public function create(Category $category): void
		{
			$this->categoryRepository->save($category, DatabaseTransaction::COMMIT);
		}
		
		public function update(Category $category): void
		{
			$this->categoryRepository->save($category);
		}
		
		public function delete(Category $category): void
		{
			$this->categoryRepository->remove($category, DatabaseTransaction::COMMIT);
		}
		
		public function findAll(?string $search = null): array
		{
			return $this->categoryRepository->search($search);
		}
		
		public function findBy(array $criteria = [], ?array $orderBy = [], ?int $limit = null,     ?int $offset = null): array
		{
			return $this->categoryRepository->findBy($criteria, $orderBy, $limit, $offset);
		}
		
		public function actives(): array
		{
			return $this->categoryRepository->findActive();
		}
	}
