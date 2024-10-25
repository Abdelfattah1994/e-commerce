<?php

namespace App\Controller\Admin;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\SupportTicketRepository;
use App\Service\OrderManager;
use App\Service\ProductManager;
use App\Service\TicketManager;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly OrderManager   $orderManager,
        private readonly ProductManager $productManager,
        private readonly UserManager    $userManager,
        private readonly TicketManager $supportTicketManager
    ) {}

    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        $today = new \DateTime();
        $todayOrders = $this->orderManager->findByDate($today);

        $stats = [
            'dailyOrders' => count($todayOrders),
            'dailyRevenue' => array_reduce($todayOrders, fn($carry, $order) => $carry + $order->getTotalAmount(), 0),
            'totalProducts' => $this->productManager->count([]),
            'totalUsers' => $this->userManager->count([]),
            'openTickets' => $this->supportTicketManager->count(['status' => 'OPEN']),
        ];

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
            'recentOrders' => $this->orderManager->recents(5),
            'topSellingProducts' => $this->productManager->topSelling(5),
        ]);
    }
}
