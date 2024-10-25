<?php

namespace App\Controller\Admin;

use App\Entity\SupportTicket;
use App\Form\SupportTicketType;
use App\Service\TicketManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/support-tickets')]
class SupportTicketController extends AbstractController
{
    public function __construct(private readonly TicketManager $supportTiketManager) {}

    #[Route('', name: 'app_admin_support_ticket_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $search = $request->query->get('q');
        return $this->render('admin/support_tickets/index.html.twig', [
            'support_tickets' => $this->supportTiketManager->findAll($search),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_support_ticket_show', methods: ['GET'])]
    public function show(SupportTicket $supportTicket): Response
    {
        return $this->render('admin/support_tickets/show.html.twig', [
            'support_ticket' => $supportTicket,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_support_ticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SupportTicket $supportTicket): Response
    {
        $form = $this->createForm(SupportTicketType::class, $supportTicket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->supportTiketManager->update($supportTicket);
            $this->addFlash('success', 'Le ticket de support a été mis à jour avec succès.');

            return $this->redirectToRoute('app_admin_support_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/support_tickets/edit.html.twig', [
            'support_ticket' => $supportTicket,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_support_ticket_delete', methods: ['POST'])]
    public function delete(Request $request, SupportTicket $supportTicket): Response
    {
        if ($this->isCsrfTokenValid('delete'.$supportTicket->getId(), $request->request->get('_token'))) {
            $this->supportTiketManager->delete($supportTicket);
            $this->addFlash('success', 'Le ticket de support a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_admin_support_ticket_index', [], Response::HTTP_SEE_OTHER);
    }
}
