<?php

namespace App\Controller\Shop;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Enums\TransactionStatusEnum;
use App\Service\CartManager;
use App\Service\VirtualBankService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/checkout')]
#[IsGranted('ROLE_USER')]
class CheckoutController extends AbstractController
{
    public function __construct(
        private CartManager $cartManager,
        private EntityManagerInterface $entityManager,
        private VirtualBankService $virtualBankService
    ) {}

    #[Route('', name: 'app_checkout')]
    public function index(#[CurrentUser] User $user): Response
    {
        $cart = $this->cartManager->getCurrentCart();

        return $this->render('shop/checkout/index.html.twig', [
            'cart' => $cart,
            'card' => $this->virtualBankService->getVirtualCardForUser($user)
        ]);
    }

    #[Route('/process', name: 'app_checkout_process', methods: ['POST'])]
    public function process(Request $request): Response
    {
        $cart = $this->cartManager->getCurrentCart();
        $user = $this->getUser();

        $order = new Order();
        $order->setUser($user);
        $order->setStatus('PENDING');
        $order->setTotalAmount($cart->getTotal());

        foreach ($cart->getItems() as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setOrder($order);
            $orderItem->setProduct($cartItem->getProduct());
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setPrice($cartItem->getProduct()->getPrice());
            $order->addItem($orderItem);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $virtualCard = $this->virtualBankService->getVirtualCardForUser($user);

        if (!$virtualCard) {
            $this->addFlash('error', 'Aucune carte virtuelle trouvée pour cet utilisateur.');
            return $this->redirectToRoute('app_checkout');
        }

        $transactionResult = $this->virtualBankService->processTransaction(
            $virtualCard,
            $order->getTotalAmount(),
            "Paiement pour la commande #{$order->getId()}"
        );

        if ($transactionResult->isSuccessful()) {
            $order->setStatus('COMPLETED');
            $order->setTransactionId($transactionResult->getTransactionId());
            $this->entityManager->flush();

            // Supprimer tous les éléments du panier
            $this->cartManager->clearCart($cart);

            return $this->redirectToRoute('app_checkout_success', ['id' => $order->getId()]);
        } else {
            $order->setStatus('FAILED');
            $this->entityManager->flush();

            $this->addFlash('error', 'Le paiement a échoué : ' . $transactionResult->getMessage());
            return $this->redirectToRoute('app_checkout');
        }
    }

    #[Route('/success/{id}', name: 'app_checkout_success')]
    public function success(Order $order): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('shop/checkout/success.html.twig', [
            'order' => $order,
        ]);
    }
}
