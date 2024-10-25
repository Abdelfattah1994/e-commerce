<?php

namespace App\Controller\Admin;

use App\Form\ContactSettingsType;
use App\Form\GeneralSettingsType;
use App\Service\SiteSettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/settings')]
class SettingsController extends AbstractController
{
    public function __construct(
        private readonly SiteSettingsService $siteSettingsService,
    ) {}

    #[Route('', name: 'app_admin_settings', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $settings = $this->siteSettingsService->getSettings();
        
        $generalForm = $this->createForm(GeneralSettingsType::class, $settings);
        $contactForm = $this->createForm(ContactSettingsType::class, $settings);

        $generalForm->handleRequest($request);
        $contactForm->handleRequest($request);

        if ($generalForm->isSubmitted() && $generalForm->isValid()) {
            $this->siteSettingsService->create($settings);
            $this->addFlash('success', 'Les paramètres généraux ont été mis à jour avec succès.');

            return $this->redirectToRoute('app_admin_settings');
        }

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $this->siteSettingsService->create($settings);
            $this->addFlash('success', 'Les paramètres de contact ont été mis à jour avec succès.');

            return $this->redirectToRoute('app_admin_settings');
        }

        return $this->render('admin/settings/index.html.twig', [
            'generalForm' => $generalForm->createView(),
            'contactForm' => $contactForm->createView(),
        ]);
    }
}
