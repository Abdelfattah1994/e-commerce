<?php
	
	namespace App\Service;
	
	use App\Entity\SiteSettings;
	use App\Enums\DatabaseTransaction;
	use App\Repository\SiteSettingsRepository;
	use Symfony\Contracts\Cache\CacheInterface;
	use Symfony\Contracts\Cache\ItemInterface;
	
	class SiteSettingsService
	{
		private ?SiteSettings $settings = null;
		
		public function __construct(
			private readonly SiteSettingsRepository $repository,
			private readonly CacheInterface $cache
		) {}
		
		public function getSettings(): SiteSettings
		{
			if (!$this->settings) {
				$this->settings = $this->cache->get('site_settings', function (ItemInterface $item) {
					$item->expiresAfter(3600); // Cache for 1 hour
					return $this->repository->findLatest() ?? new SiteSettings();
				});
			}
			
			return $this->settings;
		}
		
		public function clearCache(): void
		{
			$this->cache->delete('site_settings');
			$this->settings = null;
		}
		
		public function create(SiteSettings $settings): void
		{
			$this->repository->save($settings, DatabaseTransaction::COMMIT);
			$this->clearCache();
		}
		public function update(SiteSettings $settings): void
		{
			$this->repository->save($settings, DatabaseTransaction::COMMIT);
			$this->clearCache();
		}
	}
