<?php
	
	namespace App\Service;
	
	use App\Entity\Product;
	use App\Enums\DatabaseTransaction;
	use App\Repository\ProductRepository;
	
	readonly class ProductManager
	{
		
		public function __construct(private ProductRepository $productRepository)
		{
		}
		
		public function findAll(?string $search = null): array
		{
			return $this->productRepository->search($search);
		}
		
		public function count(array $criteria = []): int
		{
			return $this->productRepository->count($criteria);
		}
		public function filterBy(?string $search, ?int $category): array
		{
			return $this->productRepository->getFilteredProducts($search, $category);
		}
		
		public function getByCategory(int $category): array
		{
			return $this->productRepository->findByCategory($category);
		}
		
		public function getFeatured(?int $limit = 3): array
		{
			return $this->productRepository->findFeatured($limit);
		}
		public function getSimilars(Product $product, ?int $limit = 3): array
		{
			return $this->productRepository->findSimilarProducts($product, $limit);
		}
		public function findById(int $id): ?Product
		{
			return $this->productRepository->find($id);
		}
		
		public function create(Product $product): void
		{
			$this->productRepository->save($product, DatabaseTransaction::COMMIT);
		}
		
		public function update(Product $product): void
		{
			$this->productRepository->save($product);
		}
		
		public function delete(Product $product): void
		{
			$this->productRepository->remove($product, DatabaseTransaction::COMMIT);
		}
		
		public function topSelling(int $limit): array
		{
			return $this->productRepository->findTopSelling($limit);
		}
		
		public function decreaseStock(Product $product, int $quantity): void
		{
			if ($product->getStock() < $quantity) {
				throw new \Exception('Not enough stock for product: ' . $product->getName());
			}
			
			$product->setStock($product->getStock() - $quantity);
			$this->update($product);
		}
		
		public function increaseStock(Product $product, int $quantity): void
		{
			$product->setStock($product->getStock() + $quantity);
			$this->update($product);
		}
		
		public function isInStock(Product $product, int $quantity): bool
		{
			return $product->getStock() >= $quantity;
		}
		
		public function getLowStockProducts(int $threshold = 5): array
		{
			return $this->productRepository->findLowStockProducts($threshold);
		}
	}
