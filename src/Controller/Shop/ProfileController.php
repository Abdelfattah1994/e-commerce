<?php

namespace App\Controller\Shop;

use App\Repository\OrderRepository;
use App\Repository\VirtualCardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('', name: 'app_shop_profile')]
    public function index(): Response
    {
        return $this->render('shop/profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/orders', name: 'app_shop_profile_orders')]
    public function orders(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);

        return $this->render('shop/profile/orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/orders/{id}', name: 'app_shop_profile_order_show')]
    public function showOrder(int $id, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);

        if (!$order || $order->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('La commande n\'existe pas ou ne vous appartient pas.');
        }

        return $this->render('shop/profile/order_show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/cards', name: 'app_shop_profile_cards')]
    public function cards(VirtualCardRepository $virtualCardRepository): Response
    {
        $cards = $virtualCardRepository->findBy(['user' => $this->getUser()]);

        return $this->render('shop/profile/cards.html.twig', [
            'cards' => $cards,
        ]);
    }

    #[Route('/profile/receipts', name: 'app_shop_profile_receipts')]
    public function receipts(OrderRepository $orderRepository): Response
    {
        $completedOrders = $orderRepository->findCompletedByUser($this->getUser());

        return $this->render('shop/profile/receipts.html.twig', [
            'completed_orders' => $completedOrders,
        ]);
    }
}
