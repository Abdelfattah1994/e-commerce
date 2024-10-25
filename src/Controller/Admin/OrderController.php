<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\OrderManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/orders')]
class OrderController extends AbstractController
{
    public function __construct(private OrderManager $orderManager) {}

    #[Route('', name: 'app_admin_order_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $search = $request->query->get('q');

        return $this->render('admin/orders/index.html.twig', [
            'orders' => $this->orderManager->findAll($search),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('admin/orders/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/update-status', name: 'app_admin_order_update_status', methods: ['POST'])]
    public function updateStatus(Request $request, Order $order): Response
    {
        $newStatus = $request->request->get('status');
        if (in_array($newStatus, ['PENDING', 'VALIDATED', 'REFUSED', 'COMPLETED'])) {
            $this->orderManager->updateOrderStatus($order, $newStatus);
            $this->addFlash('success', 'Order status updated successfully.');
        } else {
            $this->addFlash('error', 'Invalid status provided.');
        }

        return $this->redirectToRoute('app_admin_order_show', ['id' => $order->getId()]);
    }
}
