<?php
	
	namespace App\EventListener;
	
	use App\Service\SiteSettingsService;
	use Symfony\Component\HttpKernel\Event\ControllerEvent;
	use Twig\Environment;
	
	class SiteSettingsListener
	{
		public function __construct(
			private SiteSettingsService $siteSettingsService,
			private Environment $twig
		) {}
		
		public function onKernelController(ControllerEvent $event): void
		{
			$settings = $this->siteSettingsService->getSettings();
			$this->twig->addGlobal('site_settings', $settings);
		}
	}
