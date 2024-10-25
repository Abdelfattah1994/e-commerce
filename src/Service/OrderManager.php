<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Enums\DatabaseTransaction;
use App\Event\OrderCreatedEvent;
use App\Event\OrderStatusChangedEvent;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class OrderManager
{

    public function __construct(
        private Security $security,
        private OrderRepository $orderRepository,
        private ProductManager $productManager,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function createOrderFromCart(Cart $cart): Order
    {
        $order = new Order();
        $order->setUser($this->security->getUser());
        $order->setStatus('PENDING');
        $order->setTotalAmount($cart->getTotal());

        foreach ($cart->getItems() as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setOrder($order);
            $orderItem->setProduct($cartItem->getProduct());
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setPrice($cartItem->getProduct()->getPrice());
            $order->addItem($orderItem);

            // Decrease stock
            $this->productManager->decreaseStock($cartItem->getProduct(), $cartItem->getQuantity());
        }

        $this->create($order);

        $this->eventDispatcher->dispatch(new OrderCreatedEvent($order));

        return $order;
    }

    public function getOrderById(int $id): ?Order
    {
        return $this->orderRepository->find($id);
    }

    public function updateOrderStatus(Order $order, string $status): void
    {
        $order->setStatus($status);
        $this->update($order);

        $this->eventDispatcher->dispatch(new OrderStatusChangedEvent($order));
    }
	
	private function update(Order $order): void
	{
		$this->orderRepository->save($order, DatabaseTransaction::COMMIT);
	}
	
	public function findAll(?string $search = null): array
	{
		return $this->orderRepository->search($search);
	}
	
	public function recents(int $limit = 5): array
	{
		return $this->orderRepository->findRecent($limit);
	}
	
	public function findByDate(\DateTime $date)
	{
		return $this->orderRepository->findByDate($date);
	}
	
}
