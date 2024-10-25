<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Enums\CartStatusEnum;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class CartManager
{

    public function __construct(private CartRepository $cartRepository, private EntityManagerInterface $entityManager, private Security $security)
    {
    }

    public function getCurrentCart(): Cart
    {
		/** @var User $user */
        $user = $this->security->getUser();
        if (!$user) {
            throw new \LogicException('The CartManager should only be accessed by authenticated users.');
        }

        $cart = $this->cartRepository->findOneBy(['user' => $user, 'status' => 'active']);

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setStatus(CartStatusEnum::ACTIVE->value);
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
        }

        return $cart;
    }

    public function addProduct(Product $product, int $quantity): void
    {
        $cart = $this->getCurrentCart();
        $cartItem = $this->findCartItem($cart, $product);

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        } else {
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity);
            $cart->addItem($cartItem);
        }

        $this->save($cart);
    }

    public function removeProduct(Product $product): void
    {
        $cart = $this->getCurrentCart();
        $cartItem = $this->findCartItem($cart, $product);

        if ($cartItem) {
            $cart->removeItem($cartItem);
            $this->entityManager->remove($cartItem);
            $this->save($cart);
        }
    }

    public function updateQuantity(Product $product, int $quantity): void
    {
        $cart = $this->getCurrentCart();
        $cartItem = $this->findCartItem($cart, $product);

        if ($cartItem) {
            if ($quantity <= 0) {
                $cart->removeItem($cartItem);
                $this->entityManager->remove($cartItem);
            } else {
                $cartItem->setQuantity($quantity);
            }
            $this->save($cart);
        }
    }

    private function findCartItem(Cart $cart, Product $product): ?CartItem
    {
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                return $item;
            }
        }

        return null;
    }

    public function save(Cart $cart): void
    {
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }

    public function clear(): void
    {
        $cart = $this->getCurrentCart();
        foreach ($cart->getItems() as $item) {
            $this->entityManager->remove($item);
        }
        $cart->getItems()->clear();
        $this->save($cart);
    }

    public function clearCart(Cart $cart): void
    {
        foreach ($cart->getItems() as $item) {
            $this->entityManager->remove($item);
        }
        $cart->getItems()->clear();
        $this->entityManager->flush();
    }
}
