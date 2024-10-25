<?php

namespace App\Controller\Shop;

use App\Entity\Product;
use App\Service\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    private $cartManager;

    public function __construct(CartManager $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    #[Route('', name: 'cart_index')]
    public function index(): Response
    {
        $cart = $this->cartManager->getCurrentCart();

        return $this->render('shop/cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/add/{id}', name: 'cart_add')]
    public function add(Product $product, Request $request): Response
    {
        $quantity = $request->query->getInt('quantity', 1);

        $this->cartManager->addProduct($product, $quantity);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{id}', name: 'cart_remove')]
    public function remove(Product $product): Response
    {
        $this->cartManager->removeProduct($product);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/update/{id}', name: 'cart_update')]
    public function update(Product $product, Request $request): Response
    {
        $quantity = $request->request->getInt('quantity');
        $this->cartManager->updateQuantity($product, $quantity);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/update-quantity', name: 'cart_update_quantity', methods: ['POST'])]
    public function updateQuantity(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $itemId = $data['itemId'];
        $newQuantity = $data['quantity'];

        // Logique pour mettre à jour la quantité dans le panier
        // Assurez-vous de vérifier que l'élément existe et appartient à l'utilisateur actuel

        $cart = $this->cartManager->getCurrentCart();
        $item = $cart->getItems()->filter(function($item) use ($itemId) {
            return $item->getId() == $itemId;
        })->first();

        if (!$item) {
            return new JsonResponse(['success' => false], 404);
        }

        $item->setQuantity($newQuantity);
        $this->cartManager->save($cart);

        return new JsonResponse([
            'success' => true,
            'itemTotal' => number_format($item->getTotal(), 2, ',', ' ') . ' €',
            'cartSubtotal' => number_format($cart->getTotal(), 2, ',', ' ') . ' €',
            'cartTotal' => number_format($cart->getTotal(), 2, ',', ' ') . ' €'
        ]);
    }

    public function cartItemCount(CartManager $cartManager): Response
    {
        $cart = $cartManager->getCurrentCart();
        $count = $cart ? count($cart->getItems()) : 0;

        return new Response($count);
    }
}
