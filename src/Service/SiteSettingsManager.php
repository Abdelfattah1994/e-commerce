<?php
	
	namespace App\Service;
	
	use App\Entity\SiteSettings;
	use App\Enums\DatabaseTransaction;
	use App\Repository\SiteSettingsRepository;
	
	readonly class SiteSettingsManager
	{
		
		public function __construct(private SiteSettingsRepository $siteSettingsRepository)
		{
		}
		
		public function latest(): ?SiteSettings
		{
			return $this->siteSettingsRepository->findLatest();
		}
		
		public function getSettings(): SiteSettings
		{
			$settings = $this->siteSettingsRepository->findLatest();
			if (!$settings) {
				$settings = new SiteSettings();
				$this->siteSettingsRepository->save($settings, DatabaseTransaction::COMMIT);
			}
			return $settings;
		}
		
		public function updateSettings(array $data): SiteSettings
		{
			$settings = $this->getSettings();
			
			$settings->setSiteName($data['siteName'] ?? $settings->getSiteName());
			$settings->setLogo($data['logo'] ?? $settings->getLogo());
			$settings->setFavicon($data['favicon'] ?? $settings->getFavicon());
			$settings->setHeroTitle($data['heroTitle'] ?? $settings->getHeroTitle());
			$settings->setHeroSubtitle($data['heroSubtitle'] ?? $settings->getHeroSubtitle());
			$settings->setContactEmail($data['contactEmail'] ?? $settings->getContactEmail());
			$settings->setContactPhone($data['contactPhone'] ?? $settings->getContactPhone());
			$settings->setContactAddress($data['contactAddress'] ?? $settings->getContactAddress());
			
			$this->siteSettingsRepository->save($settings, DatabaseTransaction::COMMIT);
			
			return $settings;
		}
		
		public function getContactInfo(): array
		{
			$settings = $this->getSettings();
			return [
				'email' => $settings->getContactEmail(),
				'phone' => $settings->getContactPhone(),
				'address' => $settings->getContactAddress(),
			];
		}
		
		public function getSiteInfo(): array
		{
			$settings = $this->getSettings();
			return [
				'name' => $settings->getSiteName(),
				'logo' => $settings->getLogo(),
				'favicon' => $settings->getFavicon(),
				'heroTitle' => $settings->getHeroTitle(),
				'heroSubtitle' => $settings->getHeroSubtitle(),
			];
		}
	}
