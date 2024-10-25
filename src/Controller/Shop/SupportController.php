<?php

namespace App\Controller\Shop;

use App\Entity\SupportTicket;
use App\Form\SupportTicketType;
use App\Service\TicketManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/support')]
class SupportController extends AbstractController
{
    private $ticketManager;

    public function __construct(TicketManager $ticketManager)
    {
        $this->ticketManager = $ticketManager;
    }

    #[Route('', name: 'app_shop_support_index')]
    public function index(): Response
    {
        $tickets = $this->ticketManager->getTicketsByUser($this->getUser());

        return $this->render('shop/support/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/new', name: 'app_shop_support_new')]
    public function new(Request $request): Response
    {
        $ticket = new SupportTicket();
        $form = $this->createForm(SupportTicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ticketManager->createTicket($this->getUser(), $ticket->getSubject(), $ticket->getMessage());

            $this->addFlash('success', 'Your support ticket has been submitted.');
            return $this->redirectToRoute('app_shop_support_index');
        }

        return $this->render('shop/support/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_shop_support_show')]
    public function show(SupportTicket $ticket): Response
    {
        if ($ticket->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('shop/support/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }
}
